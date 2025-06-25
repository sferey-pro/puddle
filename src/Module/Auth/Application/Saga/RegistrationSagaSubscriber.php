<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Saga;

use App\Module\Auth\Application\Command\CreateUserAccount;
use App\Module\Auth\Application\Command\DeleteUserAccount;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\SharedContext\Domain\ValueObject\Username;
use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Saga\Domain\SagaState;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\EnteredEvent;
use Symfony\Component\Workflow\StateMachine;

/**
 * Ce Subscriber est le véritable chef d'orchestre de notre Saga.
 * Il réagit aux changements d'état du workflow pour déclencher les actions.
 */
final readonly class RegistrationSagaSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private LoggerInterface $logger,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.user_registration.entered.creating_account' => 'onEnterCreatingAccount',
            'workflow.user_registration.entered.account_created' => 'onEnterAccountCreated',
            'workflow.user_registration.entered.creating_profile' => 'onEnterCreatingProfile',
            'workflow.user_registration.entered.compensating_account_creation' => 'onEnterCompensatingAccountCreation',
            'workflow.user_registration.entered.completed' => 'onCompleted',
            'workflow.user_registration.entered.failed' => 'onFailed',
        ];
    }

    // --- Actions du "Happy Path" ---

    public function onEnterCreatingAccount(EnteredEvent $event): void
    {
        /** @var SagaState $sagaState */
        $sagaState = $event->getSubject();
        $payload = $sagaState->payload();
        $this->logger->info('Saga: [Step 1] Début de création du compte.', ['sagaId' => $sagaState->id()]);

        $this->commandBus->dispatch(new CreateUserAccount(
            userId: UserId::fromString($payload['userId']),
            email: new Email($payload['email']),
            plainPassword: $payload['plainPassword'],
            username: new Username($payload['username'])
        ));
    }

    public function onEnterAccountCreated(EnteredEvent $event): void
    {
        /** @var SagaState $sagaState */
        $sagaState = $event->getSubject();
        $this->logger->info('Saga: Compte créé. Lancement de la transition vers la création de profil.', ['sagaId' => $sagaState->id()]);

        /** @var StateMachine $workflow */
        $workflow = $event->getWorkflow();
        $workflow->apply($sagaState, 'initiate_profile_creation');
        // Le save est géré par le handler "pont" qui a déclenché cette transition
    }

    public function onEnterCreatingProfile(EnteredEvent $event): void
    {
        /** @var SagaState $sagaState */
        $sagaState = $event->getSubject();
        $payload = $sagaState->payload();
        $this->logger->info('Saga: [Step 2] Début de création du profil.', ['sagaId' => $sagaState->id()]);

        $this->commandBus->dispatch(new CreateUserProfile(
            userId: UserId::fromString($payload['userId']),
            email: new Email($payload['email']),
            username: new Username($payload['username'])
        ));
    }

    // --- Actions de Compensation ---

    public function onEnterCompensatingAccountCreation(EnteredEvent $event): void
    {
        /** @var SagaState $sagaState */
        $sagaState = $event->getSubject();
        $payload = $sagaState->payload();
        $this->logger->error('Saga: Échec, [Compensation Step 1] en cours.', ['sagaId' => $sagaState->id()]);

        $this->commandBus->dispatch(new DeleteUserAccount(
            userId: UserId::fromString($payload['userId'])
        ));

        /** @var StateMachine $workflow */
        $workflow = $event->getWorkflow();
        $workflow->apply($sagaState, 'finish_account_compensation');
    }

    // --- Actions de Fin ---

    public function onCompleted(EnteredEvent $event): void
    {
        $this->logger->info('Saga: Processus terminé avec succès.', ['sagaId' => $event->getSubject()->id()]);
    }

    public function onFailed(EnteredEvent $event): void
    {
        $this->logger->error('Saga: Le processus a échoué.', ['sagaId' => $event->getSubject()->id()]);
    }
}
