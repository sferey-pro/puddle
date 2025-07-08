<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Event;

use App\Core\Domain\Event\DomainEvent;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Domain\ValueObject\SuspensionReason;

final readonly class UserSuspended extends DomainEvent
{
    public function __construct(
        private(set) UserId $aggregateId,
        private(set) SuspensionReason $reason
    ) {
        parent::__construct($this->aggregateId);
    }

    public static function eventName(): string
    {
        return 'user_management.user.account_anonymized';
    }
}
