<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Event;

use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Domain\Event\DomainEvent;

/**
 * Événement levé lorsqu'un nouvel utilisateur vérifie son compte via l'email.
 */
final readonly class UserVerified extends DomainEvent
{
    public function __construct(
        private UserId $aggregateId,
    ) {
        parent::__construct($this->aggregateId);
    }

    public static function eventName(): string
    {
        return 'auth.user.verified';
    }

    public function userId(): UserId
    {
        return $this->aggregateId;
    }
}
