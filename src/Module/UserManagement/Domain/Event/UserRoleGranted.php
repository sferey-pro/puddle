<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Event;

use App\Core\Domain\Event\DomainEvent;
use App\Module\SharedContext\Domain\ValueObject\UserId;

final readonly class UserRoleGranted extends DomainEvent
{
    public function __construct(
        private(set) UserId $aggregateId,
        private(set) Role $reason
    ) {
        parent::__construct($this->aggregateId);
    }

    public static function eventName(): string
    {
        return 'user_management.user.role_granted';
    }
}
