<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Projector;

use App\Core\Application\Clock\SystemTime;
use App\Module\Auth\Domain\Event\UserVerified;
use App\Module\UserManagement\Application\ReadModel\Repository\UserViewRepositoryInterface;
use App\Module\UserManagement\Application\ReadModel\UserView;
use App\Module\UserManagement\Domain\Enum\UserStatus;
use App\Module\UserManagement\Domain\Event\UserAccountAnonymized;
use App\Module\UserManagement\Domain\Event\UserAccountDeactivated;
use App\Module\UserManagement\Domain\Event\UserAccountReactivated;
use App\Module\UserManagement\Domain\Event\UserCreated;
use App\Module\UserManagement\Domain\Event\UserDeleted;
use App\Module\UserManagement\Domain\Event\UserEmailChanged;
use App\Module\UserManagement\Domain\Event\UserProfileUpdated;
use Psr\Log\LoggerInterface;
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
        private readonly LoggerInterface $logger,
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
    public function onUserProfileUpdated(UserProfileUpdated $event): void
    {
        $userView = $this->viewRepository->findById($event->userId());

        if (null === $userView) {
            return;
        }

        $userView->displayName = (string) $event->displayName();
        $userView->username = (string) $event->username();
        $userView->updatedAt = $this->clock->now();

        $this->viewRepository->save($userView);
    }

    #[AsMessageHandler]
    public function onUserDeleted(UserDeleted $event): void
    {
        $userView = $this->viewRepository->findById($event->userId());

        if (null === $userView) {
            return;
        }

        $this->viewRepository->delete($userView, true);
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

    #[AsMessageHandler]
    public function onUserAccountDeactivated(UserAccountDeactivated $event): void
    {
        $userView = $this->viewRepository->findById($event->userId());

        if (null === $userView) {
            return;
        }

        $userView->status = UserStatus::DEACTIVATED->value;
        $userView->updatedAt = $this->clock->now();

        $this->viewRepository->save($userView, true);
    }

    #[AsMessageHandler]
    public function onUserAccountReactivated(UserAccountReactivated $event): void
    {
        $userView = $this->viewRepository->findById($event->userId());

        if (null === $userView) {
            return;
        }

        $this->viewRepository->save($userView, true);
    }

    #[AsMessageHandler]
    public function onUserAccountAnonymized(UserAccountAnonymized $event): void
    {
        $userView = $this->viewRepository->findById($event->userId());

        if (null === $userView) {
            return;
        }

        // On nettoie les données personnelles identifiables (PII)
        $userView->displayName = 'Utilisateur Anonymisé';
        $userView->username = 'anonymized_'.$userView->id;
        $userView->firstName = null;
        $userView->lastName = null;
        $userView->email = \sprintf('%s@anonymous.puddle.com', $userView->id); // Format unique et anonyme
        $userView->avatarUrl = null;
        $userView->status = UserStatus::ANONYMIZED->value;
        $userView->updatedAt = $this->clock->now();

        $this->viewRepository->save($userView, true);
    }
}
