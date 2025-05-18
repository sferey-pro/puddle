<?php

declare(strict_types=1);

namespace App\Module\User\Application\Command;

use App\Module\Shared\Domain\ValueObject\Email;
use App\Module\Shared\Domain\ValueObject\UserId;
use App\Module\User\Domain\Model\User;
use App\Module\User\Domain\Repository\UserRepositoryInterface;
use App\Module\User\Domain\ValueObject\Name;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsCommandHandler]
final class CreateUserHandler
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private UserRepositoryInterface $repository,
    ) {
    }

    public function __invoke(CreateUser $command): void
    {
        $identifier = $command->identifier ?? UserId::generate();
        $username = $command->dto->username;
        $email = $command->dto->email;

        $user = User::create(
            identifier: $identifier,
            email: new Email($email),
            username: new Name($username),
        );

        $this->repository->save($user, true);

        foreach ($user->pullDomainEvents() as $domainEvent) {
            $this->eventDispatcher->dispatch($domainEvent);
        }
    }
}
