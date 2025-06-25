<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Event;

use App\Core\Domain\Event\DomainEvent;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Événement levé lorsqu'un compte utilisateur est suspendu pour des causes de sécurité.
 */
final readonly class UserAccountSuspended extends DomainEvent
{
    public function __construct(
        private UserId $aggregateId,
        private string $reason,
    ) {
        parent::__construct($this->aggregateId);
    }

    public static function eventName(): string
    {
        return 'auth.user.account_suspended';
    }

    public function userId(): UserId
    {
        return $this->aggregateId;
    }

    public function reason(): string
    {
        return $this->reason;
    }
}
