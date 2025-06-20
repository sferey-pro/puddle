<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Event;

use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Domain\Event\DomainEvent;

/**
 * Événement levé lorsqu'un nouvel utilisateur est créé.
 */
final readonly class UserCreated extends DomainEvent
{
    public function __construct(
        private UserId $userId,
        private Email $email,
    ) {
        parent::__construct($this->userId);
    }

    public static function eventName(): string
    {
        return 'user_management.user.created';
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function email(): Email
    {
        return $this->email;
    }
}
