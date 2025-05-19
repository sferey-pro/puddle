<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\Register;

use App\Module\Auth\Domain\Event\UserLoggedOut;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsCommandHandler]
final class LogoutUserHandler
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(LogoutUser $command): void
    {
        $event = new UserLoggedOut(identifier: $command->identifier());

        $this->eventDispatcher->dispatch($event);
    }
}
