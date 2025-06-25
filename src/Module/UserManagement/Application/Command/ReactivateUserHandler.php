<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Command;

use App\Core\Application\Event\EventBusInterface;
use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use App\Module\UserManagement\Domain\Repository\UserRepositoryInterface;

#[AsCommandHandler]
final class ReactivateUserHandler
{
    public function __construct(
        private EventBusInterface $eventBus,
        private UserRepositoryInterface $repository,
    ) {
    }

    public function __invoke(ReactivateUser $command): void
    {
        $user = $this->repository->ofId($command->userId);

        $user->reactivate();

        $this->repository->save($user, true);

        $this->eventBus->publish(...$user->pullDomainEvents());
    }
}
