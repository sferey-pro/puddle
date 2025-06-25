<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Event;

use App\Core\Domain\Event\DomainEvent;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Événement levé lorsqu'un utilisateur se déconnecte.
 */
final readonly class UserLoggedOut extends DomainEvent
{
    public function __construct(
        private UserId $aggregateId,
    ) {
        parent::__construct($this->aggregateId);
    }

    public static function eventName(): string
    {
        return 'auth.user.logged_out';
    }

    public function userId(): UserId
    {
        return $this->aggregateId;
    }
}
