<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Infrastructure\ReadModel\Projector;

use App\Module\Auth\Domain\Event\UserLoggedIn;
use App\Module\Auth\Domain\Event\UserRegistered;
use App\Module\UserManagement\Application\ReadModel\Repository\UserViewRepositoryInterface;
use App\Module\UserManagement\Domain\Event\UserEmailChanged;
use App\Module\UserManagement\Domain\Event\UserProfileUpdated;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UserProjector
{
    public function __construct(private UserViewRepositoryInterface $repository)
    {
    }

    public function __invoke(UserRegistered $event): void
    {
        // $userView = new UserView();
        // $this->repository->save($userView);
    }

    #[AsMessageHandler]
    public function handleUserEmailChanged(UserEmailChanged $event): void
    {
        // $this->repository->updateField(
        //     $event->getUserId()->toString(),
        //     'email',
        //     $event->getNewEmail()->toString()
        // );
    }

    #[AsMessageHandler]
    public function handleUserLoggedIn(UserLoggedIn $event): void
    {
        // $this->repository->updateField(
        //     $event->getUserId()->toString(),
        //     'lastLogin',
        //     $event->getLoginAt()
        // );
    }

    #[AsMessageHandler]
    public function handleUserProfileUpdated(UserProfileUpdated $event): void
    {
        // $updates = [];
        // if (null !== $event->getFirstName()) {
        //     $updates['firstName'] = $event->getFirstName();
        // }
        // if (null !== $event->getLastName()) {
        //     $updates['lastName'] = $event->getLastName();
        // }
        // // Ajoutez d'autres champs du profil ici
        // $this->repository->updateFields($event->getUserId()->toString(), $updates);
    }
}
