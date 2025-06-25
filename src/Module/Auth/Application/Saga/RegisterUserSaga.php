<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Saga;

use App\Module\Auth\Domain\Event\UserRegistered;
use App\Module\Auth\Domain\Saga\RegisterUserSagaProcess;
use App\Module\UserManagement\Application\Command\CreateUser;
use App\Module\UserManagement\Domain\Event\UserCreated;
use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Domain\Event\DomainEventInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

/** *
 * Rôle : Orchestrateur de la Saga d'inscription utilisateur.
 * Cette classe écoute les événements liés à l'inscription et pilote le processus
 * à travers les différents Bounded Contexts (Auth et UserManagement) en utilisant un workflow.
 */
#[AsMessageHandler()]
final class RegisterUserSaga
{
    public function __construct(
        private readonly WorkflowInterface $userRegistrationSagaStateMachine,
        private readonly CommandBusInterface $commandBus,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger
    ) {}

    public function __invoke(DomainEventInterface $event): void
    {
        // Le saga ne réagit qu'aux événements pour lesquels il a une logique définie
        match ($event::class) {
            UserRegistered::class => $this->handleUserRegistered($event),
            UserCreated::class => $this->handleUserCreated($event),
            default => null
        };
    }

    /**
     * Démarre la Saga lorsqu'un UserAccount est enregistré dans le module Auth.
     */
    private function handleUserRegistered(UserRegistered $event): void
    {
        $sagaProcess = new RegisterUserSagaProcess($event->eventId(), $event->userId, $event->email);

        if ($this->userRegistrationSagaStateMachine->can($sagaProcess, 'create_user_account')) {
            $this->userRegistrationSagaStateMachine->apply($sagaProcess, 'create_user_account');
            
            $this->entityManager->persist($sagaProcess);
            $this->entityManager->flush();

            // Déclenche la commande pour créer le User dans le module UserManagement
            $this->commandBus->dispatch(new CreateUser(
                $event->userId,
                $event->email,
                $event->username
            ));

            $this->logger->info('User Registration Saga started.', ['saga_id' => $sagaProcess->getId()->toString()]);
        }
    }

    /**
     * Gère la suite de la Saga lorsque le User est créé dans le module UserManagement.
     */
    private function handleUserCreated(UserCreated $event): void
    {
        /** @var RegisterUserSagaProcess|null $sagaProcess */
        $sagaProcess = $this->entityManager->getRepository(RegisterUserSagaProcess::class)->findOneBy(['userId.value' => $event->userId->toString()]);

        if (!$sagaProcess) {
            $this->logger->warning('Saga process not found for user.', ['user_id' => $event->userId->toString()]);
            return;
        }

        if ($this->userRegistrationSagaStateMachine->can($sagaProcess, 'create_user_profile')) {
            $this->userRegistrationSagaStateMachine->apply($sagaProcess, 'create_user_profile');
            // Le processus est presque terminé, on déclenche la complétion
            if ($this->userRegistrationSagaStateMachine->can($sagaProcess, 'complete')) {
                $this->userRegistrationSagaStateMachine->apply($sagaProcess, 'complete');
                $this->logger->info('User Registration Saga completed.', ['saga_id' => $sagaProcess->getId()->toString()]);
                // Ici, un autre événement pourrait être dispatché pour notifier la fin
                // ex: $this->eventBus->dispatch(new UserRegistrationSagaCompleted(...));
            }

            $this->entityManager->flush();
        }
    }
}
