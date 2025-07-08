<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\EventSubscriber;

use App\Core\Application\Command\CommandBusInterface;
use App\Module\Auth\Application\Command\DisableAuthentication;
use App\Module\UserManagement\Domain\Event\UserSuspended;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
class WhenUserSuspendedThenDisable
{
    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    public function __invoke(UserSuspended $event): void
    {
        $this->commandBus->dispatch(new DisableAuthentication(
            $event->aggregateId,
            (string) $event->reason
        ));
    }
}
