<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Event;

use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Domain\ValueObject\Name;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\DomainEventInterface;

/**
 * Événement émis lorsque le profil d'un utilisateur a été mis à jour.
 * Il contient les nouvelles informations du profil pour permettre aux projecteurs
 * de mettre à jour les Read Models sans avoir à re-interroger le Write Model.
 */
final class UserProfileUpdated extends DomainEvent implements DomainEventInterface
{
    public function __construct(
        private readonly UserId $userId,
        private readonly Name $username
    ) {
        parent::__construct();
    }

    public static function eventName(): string
    {
        return 'usermanagement.user.profile_updated';
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function username(): Name
    {
        return $this->username;
    }
}
