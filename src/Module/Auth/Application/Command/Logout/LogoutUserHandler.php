<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\Logout;

use App\Module\Auth\Domain\UserAccount;
use App\Shared\Application\Event\EventBusInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsCommandHandler]
final class LogoutUserHandler
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(LogoutUser $command): void
    {
        $user = UserAccount::logout($command->identifier());

        foreach ($user->pullDomainEvents() as $domainEvent) {
            $this->eventDispatcher->dispatch($domainEvent);
        }

        // $this->eventBus->publish(...$user->pullDomainEvents());
    }
}
