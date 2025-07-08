<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Projector;

use App\Core\Application\Clock\SystemTime;
use App\Module\Auth\Domain\Event\UserVerified;
use App\Module\UserManagement\Application\ReadModel\Repository\UserViewRepositoryInterface;
use App\Module\UserManagement\Application\ReadModel\UserView;
use App\Module\UserManagement\Domain\Event\UserCreated;
use App\Module\UserManagement\Domain\Event\UserEmailChanged;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Projecteur pour mettre à jour le ReadModel UserView.
 *
 * Ce projecteur écoute les événements de domaine et met à jour la collection
 * MongoDB 'user_views' en conséquence.
 */
class UserViewProjector
{
    public function __construct(
        private readonly UserViewRepositoryInterface $viewRepository,
        private readonly SystemTime $clock,
    ) {
    }

    #[AsMessageHandler()]
    public function onUserCreated(UserCreated $event): void
    {
        $userView = $this->viewRepository->findById($event->userId());
        if ($userView) {
            return;
        }

        $userView = new UserView((string) $event->userId());
        $userView->setEmail((string) $event->email());

        $this->viewRepository->save($userView, true);
    }

    #[AsMessageHandler]
    public function onUserEmailChanged(UserEmailChanged $event): void
    {
        $userView = $this->viewRepository->findById($event->userId());

        if (null === $userView) {
            return;
        }

        $userView->setEmail((string) $event->newEmail());

        $this->viewRepository->save($userView, true);
    }

    #[AsMessageHandler]
    public function onUserVerified(UserVerified $event): void
    {
        $userView = $this->viewRepository->findById($event->userId());

        if (null === $userView) {
            return;
        }

        $userView->setIsVerified(true);

        $this->viewRepository->save($userView, true);
    }
}
