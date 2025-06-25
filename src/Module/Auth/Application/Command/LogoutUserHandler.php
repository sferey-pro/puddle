<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Core\Application\Event\EventBusInterface;
use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use App\Module\Auth\Domain\UserAccount;
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
        $user = UserAccount::logout($command->id);
        $this->eventBus->publish(...$user->pullDomainEvents());
    }
}
