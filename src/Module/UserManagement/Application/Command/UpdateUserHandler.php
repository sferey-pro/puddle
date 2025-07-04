<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Command;

use App\Core\Application\Event\EventBusInterface;
use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use App\Module\UserManagement\Domain\Repository\UserRepositoryInterface;
use App\Module\UserManagement\Domain\ValueObject\Name;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommandHandler]
final class UpdateUserHandler
{
    public function __construct(
        private EventBusInterface $eventBus,
        private UserRepositoryInterface $userRepository,
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(UpdateUser $command): void
    {
        $user = $this->userRepository->ofId($command->userId);

        $name = new Name(
            $command->dto->firstName,
            $command->dto->lastName
        );

        $user->updateProfile($name);

        $this->userRepository->add($user);
        $this->em->flush();

        $this->eventBus->publish(...$user->pullDomainEvents());
    }
}
