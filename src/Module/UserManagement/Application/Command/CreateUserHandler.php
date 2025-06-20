<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Command;

use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Domain\Exception\UserException;
use App\Module\UserManagement\Domain\Repository\UserRepositoryInterface;
use App\Module\UserManagement\Domain\Specification\UniqueEmailSpecification;
use App\Module\UserManagement\Domain\User;
use App\Shared\Application\Event\EventBusInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;

#[AsCommandHandler]
final class CreateUserHandler
{
    public function __construct(
        private EventBusInterface $eventBus,
        private UserRepositoryInterface $repository,
        private UniqueEmailSpecification $uniqueEmailSpecification,
    ) {
    }

    public function __invoke(CreateUser $command): void
    {
        $dto = $command->dto;

        $email = new Email($dto->email);
        if (!$this->uniqueEmailSpecification->isSatisfiedBy($email)) {
            throw UserException::emailAlreadyExists($email);
        }

        $user = User::create(
            $command->userId ?? UserId::generate(),
            $email
        );
        $this->repository->save($user, true);

        $this->eventBus->publish(...$user->pullDomainEvents());
    }
}
