<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Core\Application\Clock\ClockInterface;
use App\Core\Application\Event\EventBusInterface;
use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommandHandler]
final readonly class RecordLoginLinkFailureHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private EventBusInterface $eventBus,
        private ClockInterface $clock,
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(RecordLoginLinkFailure $command): void
    {
        $user = $this->userRepository->ofId($command->userId);

        // On appelle la méthode de l'agrégat qui contient la logique
        $user->recordLoginFailure($this->clock);

        $this->userRepository->add($user);
        $this->em->flush();

        $this->eventBus->publish(...$user->pullDomainEvents());
    }
}
