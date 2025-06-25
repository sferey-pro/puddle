<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Saga;

use App\Shared\Saga\Application\SagaActionCommandInterface;
use App\Shared\Saga\Domain\Repository\SagaStateRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Ce listener est le filet de sécurité de notre Saga.
 * Il écoute les échecs des commandes qui font partie d'une saga et
 * déclenche la transition d'échec correspondante sur le workflow.
 */
#[AsEventListener(event: WorkerMessageFailedEvent::class)]
final readonly class SagaFailureListener
{
    public function __construct(
        private SagaStateRepositoryInterface $sagaStateRepository,
        private WorkflowInterface $userRegistrationStateMachine,
        private Registry $workflowRegistry,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(WorkerMessageFailedEvent $event): void
    {
        $command = $event->getEnvelope()->getMessage();

        // On ne s'intéresse qu'aux commandes qui font partie d'une saga
        if (!$command instanceof SagaActionCommandInterface) {
            return;
        }

        // On ne gère pas les erreurs durant la nouvelle tentative (retry)
        if ($event->willRetry()) {
            return;
        }

        $correlationId = $command->getCorrelationId()->value;
        $this->logger->critical('Saga: Échec détecté pour une action.', ['correlationId' => $correlationId, 'command' => $command::class]);

        $sagaState = $this->sagaStateRepository->findOneByPayload('userId', $correlationId);

        if (null === $sagaState) {
            $this->logger->error('Impossible de trouver une saga en cours à faire échouer.', ['correlationId' => $correlationId]);
            return;
        }

        // On récupère dynamiquement le bon workflow grâce au nom stocké dans sagaType
        $workflow = $this->workflowRegistry->get($sagaState, $sagaState->getSagaType());

        // On détermine la bonne transition d'échec en fonction de l'état actuel de la saga
        $transition = match ($sagaState->getStatus()) {
            'creating_account' => 'fail_account_creation',
            'creating_profile' => 'fail_profile_creation',
            default => null
        };

        if (null === $transition) {
            $this->logger->error('Aucune transition d\'échec définie pour l\'état actuel de la saga.', ['status' => $sagaState->getStatus()]);
            return;
        }

        if ($workflow->can($sagaState, $transition)) {
            $workflow->apply($sagaState, $transition);
            $this->sagaStateRepository->save($sagaState, true);
            $this->logger->info('Saga basculée en mode compensation.', ['sagaId' => $sagaState->id(), 'transition' => $transition]);
        }
    }
}
