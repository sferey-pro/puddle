<?php

declare(strict_types=1);

namespace Account\Registration\Application\Saga\Step;

use Account\Registration\Application\Command\CompensateAccountCreation;
use Account\Registration\Application\Command\CreateAccount;
use Account\Registration\Domain\Saga\Process\RegistrationSagaProcess;
use Kernel\Application\Bus\CommandBusInterface;
use Kernel\Application\Saga\Process\SagaProcessInterface;
use Kernel\Application\Saga\Step\Attribute\SagaStep;
use Kernel\Application\Saga\Step\SagaStepInterface;

#[SagaStep('create_user', 'registration')]
final readonly class CreateAccountStep implements SagaStepInterface
{
    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    /**
     * Déclenche la création du compte.
     */
    public function execute(SagaProcessInterface $sagaProcess): void
    {
        if (!$sagaProcess instanceof RegistrationSagaProcess) {
            throw new \LogicException('Cette étape ne peut être exécutée que pour une RegistrationSagaProcess.');
        }

        $this->commandBus->dispatch(
            new CreateAccount(
                $sagaProcess->userId(),
                $sagaProcess->identifier(),
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
            new CompensateAccountCreation(
                $sagaProcess->userId()
            )
        );
    }
}
