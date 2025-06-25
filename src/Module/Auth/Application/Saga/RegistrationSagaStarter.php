<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Saga;

use App\Module\Auth\Application\Command\StartRegistrationSaga;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Saga\Domain\Repository\SagaStateRepositoryInterface;
use App\Shared\Saga\Domain\SagaState;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Amorceur de la Saga d'inscription.
 * Son seul rôle est de créer l'état de la saga et de déclencher la première transition du workflow.
 */
#[AsMessageHandler]
final readonly class RegistrationSagaStarter
{
    public function __construct(
        private SagaStateRepositoryInterface $sagaStateRepository,
        private WorkflowInterface $userRegistrationStateMachine,
    ) {
    }

    public function __invoke(StartRegistrationSaga $command): void
    {
        $userId = UserId::generate();
        $sagaState = new SagaState(
            $this->sagaStateRepository->nextIdentity(),
            'user_registration',
            [
                'userId' => $userId->toString(),
                'email' => $command->email->value,
                'username' => $command->username->value,
                'plainPassword' => $command->plainPassword,
            ]
        );

        $this->userRegistrationStateMachine->apply($sagaState, 'start');
        $this->sagaStateRepository->save($sagaState, true);
    }
}
