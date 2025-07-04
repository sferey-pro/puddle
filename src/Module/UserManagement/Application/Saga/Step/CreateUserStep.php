<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Saga\Step;

use App\Core\Application\Command\CommandBusInterface;
use App\Core\Application\Saga\Process\SagaProcessInterface;
use App\Core\Application\Saga\Step\SagaStepInterface;
use App\Module\Auth\Domain\Saga\Process\RegistrationSagaProcess;
use App\Module\UserManagement\Application\Command\CompensateUserCreation;
use App\Module\UserManagement\Application\Command\CreateUser;

/**
 * Étape du Saga responsable de la création du profil utilisateur.
 */
final readonly class CreateUserStep implements SagaStepInterface
{
    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    /**
     * Déclenche la création du profil utilisateur.
     */
    public function execute(SagaProcessInterface $sagaProcess): void
    {
        /** @var RegistrationSagaProcess $sagaProcess */
        $this->commandBus->dispatch(
            new CreateUser(
                $sagaProcess->userId(),
                $sagaProcess->email()
            )
        );
    }

    /**
     * Déclenche l'annulation de la création du profil utilisateur.
     */
    public function compensate(SagaProcessInterface $sagaProcess): void
    {
        /** @var RegistrationSagaProcess $sagaProcess */
        $this->commandBus->dispatch(
            new CompensateUserCreation($sagaProcess->userId())
        );
    }
}
