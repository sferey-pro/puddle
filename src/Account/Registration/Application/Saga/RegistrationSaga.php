<?php

declare(strict_types=1);

namespace Account\Registration\Application\Saga;

use Account\Registration\Application\Event\RegistrationProcessCompleted;
use Account\Registration\Application\Saga\Event\RegistrationSagaStarted;
use Account\Registration\Domain\Repository\RegistrationProcessRepositoryInterface;
use Account\Registration\Domain\Saga\Process\RegistrationSagaProcess;
use Kernel\Application\Clock\ClockInterface;
use Kernel\Application\Saga\Process\SagaProcessInterface;
use Kernel\Application\Saga\Step\SagaStepRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\EventDispatcher\EventDispatcher;
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
        private RegistrationProcessRepositoryInterface $processRepository,
        private LoggerInterface $logger,
        private EventDispatcher $eventDispatcher,
        private ClockInterface $clock
    ) {
    }

    /**
     * Gère l'événement de démarrage du Saga. C'est le point d'entrée de l'orchestration.
     */
    public function __invoke(RegistrationSagaStarted $event): void
    {
        /** @var RegistrationSagaProcess|null $sagaProcess */
        $sagaProcess = $this->processRepository->ofId($event->sagaStateId());

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
                $this->logger->info('Saga process has reached a final state.', ['saga_id' => $sagaProcess->id]);
                break;
            }

            $transitionName = $transitions[0]->getName();
            if (!$this->stepRegistry->hasStep($transitionName)) {
                $this->logger->info('No step found for transition, stopping automated progression.', ['transition' => $transitionName]);
                break;
            }

            $step = $this->stepRegistry->getStep($transitionName, 'registration');

            try {
                $this->logger->info('Saga: Executing step for transition.', ['transition' => $transitionName]);
                $step->execute($sagaProcess);

                $this->workflow->apply($sagaProcess, $transitionName);
                $sagaProcess->addTransitionToHistory($transitionName);

                $this->processRepository->save($sagaProcess);
            } catch (\Throwable $e) {
                $this->handleFailure($sagaProcess, $e);

                $this->processRepository->save($sagaProcess);
                throw $e;
            }
        }

        if ($this->workflow->can($sagaProcess, 'complete')) {
            $this->workflow->apply($sagaProcess, 'complete');
            $this->logger->info('Saga completed successfully.');

            $completionEvent = new RegistrationProcessCompleted(
                $sagaProcess->userId(),
                $this->clock->now()
            );

            $this->eventDispatcher->dispatch($completionEvent);
            $this->processRepository->save($sagaProcess);
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
            $step = $this->stepRegistry->getStep($transitionToCompensate, 'registration');
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
