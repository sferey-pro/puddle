<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Saga;

use App\Module\Auth\Domain\Event\UserAccountCreated;
use App\Module\Auth\Domain\Event\UserRegistered;
use App\Shared\Saga\Domain\Repository\SagaStateRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Ce handler est un "pont". Il écoute un événement métier (`UserAccountCreated`)
 * et le traduit en une action sur le workflow de la Saga.
 * C'est la colle qui permet de faire avancer le processus de manière découplée.
 */
#[AsMessageHandler]
final readonly class AdvanceSagaOnUserAccountCreated
{
    public function __construct(
        private SagaStateRepositoryInterface $sagaStateRepository,
        private WorkflowInterface $userRegistrationStateMachine,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(UserRegistered $event): void
    {
        $this->logger->info('Pont: UserAccountCreated reçu, tentative de faire avancer la saga.', ['userId' => $event->aggregateId()]);

        // On utilise l'ID de l'utilisateur comme ID de corrélation pour retrouver la saga
        $sagaState = $this->sagaStateRepository->findOneByPayload('userId', $event->aggregateId());

        if (null === $sagaState) {
            $this->logger->warning('Aucune saga en cours trouvée pour cet userId.', ['userId' => $event->aggregateId()]);
            return;
        }

        // On vérifie si la transition est possible avant de l'appliquer
        if ($this->userRegistrationStateMachine->can($sagaState, 'account_created')) {
            $this->userRegistrationStateMachine->apply($sagaState, 'account_created');
            $this->sagaStateRepository->save($sagaState, true);
            $this->logger->info('Saga avancée à l\'étape suivante.', ['sagaId' => $sagaState->id()]);
        } else {
            $this->logger->error('Transition "account_created" impossible pour la saga.', [
                'sagaId' => $sagaState->id(),
                'currentStatus' => $sagaState->status()
            ]);
        }
    }
}
