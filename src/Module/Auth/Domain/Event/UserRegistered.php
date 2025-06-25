<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Event;

use App\Core\Domain\Event\DomainEvent;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Événement levé lorsqu'un nouvel utilisateur s'enregistre.
 */
final readonly class UserRegistered extends DomainEvent
{
    public function __construct(
        private UserId $aggregateId,
        private Email $email,
    ) {
        parent::__construct($this->aggregateId);
    }

    public static function eventName(): string
    {
        return 'auth.user.registered';
    }

    public function userId(): UserId
    {
        return $this->aggregateId;
    }

    public function email(): Email
    {
        return $this->email;
    }
}
