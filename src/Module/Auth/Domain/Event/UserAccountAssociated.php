<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Event;

use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Domain\Event\DomainEvent;

/**
 * Événement levé lorsqu'un nouvel utilisateur est associé.
 */
final readonly class UserAccountAssociated extends DomainEvent
{
    public function __construct(
        private UserId $aggregateId,
        private Email $email,
    ) {
        parent::__construct($this->aggregateId);
    }

    public static function eventName(): string
    {
        return 'auth.user_account.associated';
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
