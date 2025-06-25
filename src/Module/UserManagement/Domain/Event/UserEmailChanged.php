<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Event;

use App\Core\Domain\Event\DomainEvent;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Événement levé lorsque l'adresse e-mail d'un utilisateur est modifiée.
 */
final readonly class UserEmailChanged extends DomainEvent
{
    public function __construct(
        private UserId $userId,
        private Email $newEmail,
        private Email $oldEmail,
    ) {
        parent::__construct($this->userId);
    }

    public static function eventName(): string
    {
        return 'user_management.user.email_changed';
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function newEmail(): Email
    {
        return $this->newEmail;
    }

    public function oldEmail(): Email
    {
        return $this->oldEmail;
    }
}
