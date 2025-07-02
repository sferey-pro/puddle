<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Command;

use App\Core\Application\Event\EventBusInterface;
use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use App\Module\UserManagement\Domain\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommandHandler]
final class AnonymizeUserHandler
{
    public function __construct(
        private EventBusInterface $eventBus,
        private UserRepositoryInterface $userRepository,
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(AnonymizeUser $command): void
    {
        $user = $this->userRepository->ofId($command->userId);

        $user->anonymize();

        $this->userRepository->add($user);
        $this->em->flush();

        $this->eventBus->publish(...$user->pullDomainEvents());
    }
}
