<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Event;

use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Domain\ValueObject\DisplayName;
use App\Module\UserManagement\Domain\ValueObject\Username;
use App\Shared\Domain\Event\DomainEvent;

/**
 * Événement levé lorsque le profil d'un utilisateur a été mis à jour.
 */
final readonly class UserProfileUpdated extends DomainEvent
{
    public function __construct(
        private UserId $userId,
        private Username $username,
        private DisplayName $displayName,
    ) {
        parent::__construct($this->userId);
    }

    public static function eventName(): string
    {
        return 'user_management.user.profile_updated';
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function username(): Username
    {
        return $this->username;
    }

    public function displayName(): DisplayName
    {
        return $this->displayName;
    }
}
