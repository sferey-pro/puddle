<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Saga;

use App\Core\Application\Saga\Process\SagaProcessInterface;
use App\Core\Application\Saga\Step\SagaStepRegistry;
use App\Module\Auth\Application\Saga\Event\RegistrationSagaStarted;
use App\Module\Auth\Domain\Saga\Process\RegistrationSagaProcess;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Orchestre le "Parcours Métier d'Inscription" de l'utilisateur.
 *
 * Rôle métier :
 * Cette classe est le "chef d'orchestre" de notre Saga. Elle est le seul endroit
 * où la séquence des étapes du parcours d'inscription est définie et pilotée.
 *
 * Son rôle est de :
 * 1.  Recevoir le signal de départ (`RegistrationSagaStarted`).
 * 2.  Faire avancer le parcours étape par étape en s'appuyant sur la machine à états (Workflow).
 * 3.  Pour chaque étape, déclencher l'action correspondante (ex: "Créer le compte").
 * 4.  En cas d'échec d'une étape, orchestrer la "marche arrière" (compensation) pour
 * annuler ce qui a déjà été fait et garantir que le système reste cohérent.
 *
 * Cette centralisation rend le parcours d'inscription lisible, maintenable et robuste.
 */
#[AsMessageHandler]
final class RegistrationSaga
{
    public function __construct(
        #[Target('registration_saga')]
        private WorkflowInterface $workflow,
        private SagaStepRegistry $stepRegistry,
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Gère l'événement de démarrage du Saga. C'est le point d'entrée de l'orchestration.
     */
    public function __invoke(RegistrationSagaStarted $event): void
    {
        /** @var RegistrationSagaProcess|null $sagaProcess */
        $sagaProcess = $this->em->find(RegistrationSagaProcess::class, $event->sagaStateId());

        // Si le parcours n'existe pas ou est déjà terminé, on ne fait rien.
        if (null === $sagaProcess || $this->workflow->can($sagaProcess, 'complete')) {
            return;
        }

        $this->proceed($sagaProcess);
    }

    /**
     * Fait avancer le parcours étape par étape.
     *
     * @param RegistrationSagaProcess $sagaProcess
     */
    private function proceed(SagaProcessInterface $sagaProcess): void
    {
        while ($transitions = $this->workflow->getEnabledTransitions($sagaProcess)) {
            if (empty($transitions)) {
                $this->logger->info('Saga process has reached a final state.', ['saga_id' => $sagaProcess->id()]);
                break;
            }

            $transitionName = $transitions[0]->getName();
            if (!$this->stepRegistry->hasStep($transitionName)) {
                $this->logger->info('No step found for transition, stopping automated progression.', ['transition' => $transitionName]);
                break;
            }

            $step = $this->stepRegistry->getStep($transitionName);

            try {
                $this->logger->info('Saga: Executing step for transition.', ['transition' => $transitionName]);
                $step->execute($sagaProcess);

                $this->workflow->apply($sagaProcess, $transitionName);
                $sagaProcess->addTransitionToHistory($transitionName);

                $this->em->flush();
            } catch (\Throwable $e) {
                $this->handleFailure($sagaProcess, $e);

                $this->em->flush();
                throw $e;
            }
        }

        if ($this->workflow->can($sagaProcess, 'complete')) {
            $this->workflow->apply($sagaProcess, 'complete');
            $this->logger->info('Saga completed successfully.');

            $this->em->flush();
        }
    }

    /**
     * Gère l'échec d'une étape en lançant le processus de compensation.
     */
    private function handleFailure(SagaProcessInterface $sagaProcess, \Throwable $e): void
    {
        $this->logger->error('Saga step failed, starting compensation.', ['exception' => $e->getMessage()]);

        $compensationSucceeded = $this->compensate($sagaProcess);

        $finalState = $compensationSucceeded ? 'mark_as_compensated' : 'mark_as_compensation_failed';
        $this->workflow->apply($sagaProcess, $finalState);
    }

    /**
     * Exécute les actions de compensation en ordre inverse.
     *
     * @return bool true si toutes les compensations ont réussi
     */
    private function compensate(SagaProcessInterface $sagaProcess): bool
    {
        $allSucceeded = true;
        foreach ($sagaProcess->history() as $transitionToCompensate) {
            $step = $this->stepRegistry->getStep($transitionToCompensate);
            try {
                $this->logger->info('Saga: Compensating step for transition.', ['transition' => $transitionToCompensate]);
                $step->compensate($sagaProcess);
            } catch (\Throwable $e) {
                $allSucceeded = false;
                $this->logger->critical('Saga compensation failed.', ['transition' => $transitionToCompensate, 'exception' => $e]);
            }
        }

        return $allSucceeded;
    }
}
