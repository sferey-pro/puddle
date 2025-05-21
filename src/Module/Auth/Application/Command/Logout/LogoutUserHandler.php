<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\Logout;

use App\Module\Auth\Domain\Event\UserLoggedOut;
use App\Module\Auth\Domain\UserAccount;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommandHandler]
final class LogoutUserHandler
{
    public function __construct(
        private MessageBusInterface $eventBus,
    ) {
    }

    public function __invoke(LogoutUser $command): void
    {
        $user = UserAccount::logout($command->identifier());

        foreach ($user->pullDomainEvents() as $domainEvent) {
            $this->eventBus->dispatch($domainEvent);
        }
    }
}
