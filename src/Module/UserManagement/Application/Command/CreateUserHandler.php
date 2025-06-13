<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Command;

use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Domain\Repository\UserRepositoryInterface;
use App\Module\UserManagement\Domain\User;
use App\Module\UserManagement\Domain\ValueObject\Name;
use App\Shared\Application\Event\EventBusInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsCommandHandler]
final class CreateUserHandler
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private EventBusInterface $eventBus,
        private UserRepositoryInterface $repository,
    ) {
    }

    public function __invoke(CreateUser $command): void
    {
        $id = $command->id ?? UserId::generate();
        $username = $command->dto->username;
        $email = $command->dto->email;

        $user = User::create(
            id: $id,
            email: new Email($email),
            username: new Name($username),
        );

        $this->repository->save($user, true);

        foreach ($user->pullDomainEvents() as $domainEvent) {
            $this->eventDispatcher->dispatch($domainEvent);
        }

        // $this->eventBus->publish(...$user->pullDomainEvents());
    }
}
