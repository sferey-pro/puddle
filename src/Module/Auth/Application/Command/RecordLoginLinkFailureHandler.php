<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Shared\Application\Event\EventBusInterface;
use App\Shared\Domain\Service\ClockInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;

#[AsCommandHandler]
final readonly class RecordLoginLinkFailureHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private EventBusInterface $eventBus,
        private ClockInterface $clock,
    ) {
    }

    public function __invoke(RecordLoginLinkFailure $command): void
    {
        $user = $this->userRepository->ofId($command->userId);

        // On appelle la méthode de l'agrégat qui contient la logique
        $user->recordLoginFailure($this->clock);

        $this->userRepository->save($user);
        $this->eventBus->publish(...$user->pullDomainEvents());
    }
}
