<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Command;

use App\Module\UserManagement\Domain\Repository\UserRepositoryInterface;
use App\Shared\Application\Event\EventBusInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;

#[AsCommandHandler]
final class AnonymizeUserHandler
{
    public function __construct(
        private EventBusInterface $eventBus,
        private UserRepositoryInterface $repository,
    ) {
    }

    public function __invoke(AnonymizeUser $command): void
    {
        $user = $this->repository->ofId($command->userId);

        $user->anonymize();

        $this->repository->save($user, true);

        $this->eventBus->publish(...$user->pullDomainEvents());
    }
}
