<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Shared\Application\Event\EventBusInterface;
use App\Shared\Domain\Service\ClockInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;

#[AsCommandHandler]
final readonly class VerifyLoginLinkHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private EventBusInterface $eventBus,
        private ClockInterface $clock,
    ) {
    }

    public function __invoke(VerifyLoginLink $command): void
    {
        $user = $this->userRepository->ofId($command->userId);

        // L'agrégat gère la logique de vérification
        $user->verifyLoginLink($command->hash, $this->clock);

        $this->userRepository->save($user, true);

        $this->eventBus->publish(...$user->pullDomainEvents());
    }
}
