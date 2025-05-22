<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Projector;

use App\Module\Auth\Domain\Event\UserRegistered;
use App\Module\Auth\Domain\Event\UserVerified;
use App\Module\UserManagement\Application\ReadModel\Repository\UserViewRepositoryInterface;
use App\Module\UserManagement\Application\ReadModel\UserView;
use App\Module\UserManagement\Domain\Event\UserCreated;
use App\Module\UserManagement\Domain\Event\UserDeleted;
use App\Module\UserManagement\Domain\Event\UserEmailChanged;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Projecteur pour mettre à jour le ReadModel UserView.
 *
 * Ce projecteur écoute les événements de domaine et met à jour la collection
 * MongoDB 'user_views' en conséquence.
 */
class UserViewProjector implements EventSubscriberInterface
{
    public function __construct(
        private readonly UserViewRepositoryInterface $userViewRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        // S'abonne aux événements concernant les données UserView modifié par les Bounded Context UserManagement et Auth
        return [
            // Auth Event
            UserRegistered::class => 'onUserRegistered',
            UserVerified::class => 'onUserVerified',

            // UserManagement Event
            UserCreated::class => 'onUserCreated',
            UserDeleted::class => 'onUserDeleted',
            UserEmailChanged::class => 'onUserEmailChanged',
        ];
    }

    public function onUserRegistered(UserRegistered $event): void
    {
        $existingView = $this->userViewRepository->findById($event->identifier());
        if ($existingView) {
            return;
        }

        $userView = new UserView(
            userId: (string) $event->identifier(),
        );

        $userView->setEmail((string) $event->email());

        $this->userViewRepository->save($userView, true);
    }

    public function onUserCreated(UserCreated $event): void
    {
        $existingView = $this->userViewRepository->findById($event->identifier());
        if ($existingView) {
            return;
        }

        $userView = new UserView(
            userId: (string) $event->identifier(),
        );

        $userView->setEmail((string) $event->email());

        $this->userViewRepository->save($userView, true);
    }

    public function onUserDeleted(UserDeleted $event): void
    {
        $existingView = $this->userViewRepository->findById($event->identifier());

        if (null === $existingView) {
            return;
        }

        $userView = new UserView(
            userId: (string) $event->identifier(),
        );

        $this->userViewRepository->delete($userView, true);
    }

    public function onUserEmailChanged(UserEmailChanged $event): void
    {
        $existingView = $this->userViewRepository->findById($event->identifier());

        if (null === $existingView) {
            return;
        }

        $existingView->setEmail((string) $event->email());

        $this->userViewRepository->save($existingView, true);
    }

    public function onUserVerified(UserVerified $event): void
    {
        $existingView = $this->userViewRepository->findById($event->identifier());

        if (null === $existingView) {
            return;
        }

        $existingView->setIsVerified($event->verified());

        $this->userViewRepository->save($existingView, true);
    }
}
