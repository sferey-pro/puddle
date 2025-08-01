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
use Psr\Log\LoggerInterface;

/**
 * Step de création du compte dans le contexte Account.
 */
#[SagaStep('create_user', 'registration')]
final readonly class CreateAccountStep implements SagaStepInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private LoggerInterface $logger
    ) {
    }

    /**
     * Déclenche la création du compte.
     * @param RegistrationSagaProcess $sagaProcess
     */
    public function execute(SagaProcessInterface $sagaProcess): void
    {
        $this->support($sagaProcess);

        $userId = $sagaProcess->userId();
        $identifier = $sagaProcess->identifier();

        $this->logger->info('Creating account via Account context', [
            'saga_id' => (string) $sagaProcess->id,
            'user_id' => (string) $userId
        ]);

        $this->commandBus->dispatch(
            new CreateAccount(
                $userId,
                $identifier,
            )
        );
    }

    /**
     * Déclenche l'annulation de la création du compte.
     * @param RegistrationSagaProcess $sagaProcess
     */
    public function compensate(SagaProcessInterface $sagaProcess): void
    {
        $this->support($sagaProcess);

        $this->commandBus->dispatch(
            new CompensateAccountCreation(
                $sagaProcess->userId()
            )
        );
    }

    private function support(SagaProcessInterface $sagaProcess) {

        if (!$sagaProcess instanceof RegistrationSagaProcess) {
            throw new \LogicException('Cette étape ne peut être exécutée ou compensée que pour une RegistrationSagaProcess.');
        }
    }
}
