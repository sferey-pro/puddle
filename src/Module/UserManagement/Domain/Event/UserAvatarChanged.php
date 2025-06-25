<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Event;

use App\Core\Domain\Event\DomainEvent;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Domain\ValueObject\AvatarUrl;

/**
 * Événement levé lorsque l'avatar d'un utilisateur est modifié.
 */
final readonly class UserAvatarChanged extends DomainEvent
{
    public function __construct(
        private UserId $userId,
        private AvatarUrl $newAvatarUrl,
        private ?AvatarUrl $oldAvatarUrl = null,
    ) {
        parent::__construct($this->userId);
    }

    public static function eventName(): string
    {
        return 'user_management.user.avatar_changed';
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function newAvatarUrl(): AvatarUrl
    {
        return $this->newAvatarUrl;
    }

    public function oldAvatarUrl(): ?AvatarUrl
    {
        return $this->oldAvatarUrl;
    }
}
