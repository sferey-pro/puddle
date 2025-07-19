<?php

declare(strict_types=1);

namespace Account\Registration\Application\Service;

use Account\Registration\Application\Command\StartRegistrationSaga;
use SharedKernel\Domain\Service\AccountRegistrationContextInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Service d'orchestration pour les processus cross-contextes
 */
final class AccountRegistrationContextService implements AccountRegistrationContextInterface
{
    public function __construct(
        private readonly MessageBusInterface $commandBus
    ) {}

    public function initiateRegistration(
        string $identifier,
        string $ipAddress
    ): void {
        $this->commandBus->dispatch(new StartRegistrationSaga(
            identifier: $identifier,
            ipAddress: $ipAddress
        ));
    }
}
