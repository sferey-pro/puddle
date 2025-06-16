<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Command;

use App\Module\UserManagement\Domain\Repository\UserRepositoryInterface;
use App\Module\UserManagement\Domain\ValueObject\Name;
use App\Shared\Application\Event\EventBusInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;

#[AsCommandHandler]
final class UpdateUserHandler
{
    public function __construct(
        private EventBusInterface $eventBus,
        private UserRepositoryInterface $repository,
    ) {
    }

    public function __invoke(UpdateUser $command): void
    {
        $user = $this->repository->ofId($command->userId);

        $name = new Name(
            $command->dto->firstName,
            $command->dto->lastName
        );

        $user->updateProfile($name);

        $this->repository->save($user, true);

        $this->eventBus->publish(...$user->pullDomainEvents());
    }
}
