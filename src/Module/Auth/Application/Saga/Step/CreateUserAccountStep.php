<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Saga\Step;

use App\Core\Application\Command\CommandBusInterface;
use App\Core\Application\Saga\Process\SagaProcessInterface;
use App\Core\Application\Saga\Step\SagaStepInterface;
use App\Module\Auth\Application\Command\Register\CompensateUserAccountCreation;
use App\Module\Auth\Application\Command\Register\CreateUserAccount;
use App\Module\Auth\Domain\Saga\Process\RegistrationSagaProcess;

/**
 * Étape du Saga responsable de la création du compte d'authentification.
 */
final readonly class CreateUserAccountStep implements SagaStepInterface
{
    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    /**
     * Déclenche la création du compte d'authentification.
     */
    public function execute(SagaProcessInterface $sagaProcess): void
    {
        /** @var RegistrationSagaProcess $sagaProcess */
        $this->commandBus->dispatch(
            new CreateUserAccount(
                $sagaProcess->userId(),
                $sagaProcess->email(),
            )
        );
    }

    /**
     * Déclenche l'annulation de la création du compte.
     */
    public function compensate(SagaProcessInterface $sagaProcess): void
    {
        /** @var RegistrationSagaProcess $sagaProcess */
        $this->commandBus->dispatch(
            new CompensateUserAccountCreation($sagaProcess->userId())
        );
    }
}
