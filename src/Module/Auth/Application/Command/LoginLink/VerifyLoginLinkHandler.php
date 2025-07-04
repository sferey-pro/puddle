<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\LoginLink;

use App\Core\Application\Clock\ClockInterface;
use App\Core\Application\Event\EventBusInterface;
use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;

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

        $this->userRepository->add($user);

        $this->eventBus->publish(...$user->pullDomainEvents());
    }
}
