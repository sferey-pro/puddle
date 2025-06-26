<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Command;

use App\Core\Application\Event\EventBusInterface;
use App\Core\Domain\Specification\IsUniqueSpecification;
use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Domain\Exception\UserException;
use App\Module\UserManagement\Domain\Repository\UserRepositoryInterface;
use App\Module\UserManagement\Domain\User;

#[AsCommandHandler]
final class CreateUserHandler
{
    public function __construct(
        private EventBusInterface $eventBus,
        private UserRepositoryInterface $repository,
    ) {
    }

    public function __invoke(CreateUser $command): void
    {
        $dto = $command->dto;

        $email = new Email($dto->email);
        $spec = new IsUniqueSpecification($email);

        // On demande au repository de la vérifier.
        if (0 !== $this->repository->countBySpecification($spec)) {
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
