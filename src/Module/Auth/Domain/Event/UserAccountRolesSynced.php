<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Event;

use App\Core\Domain\Event\DomainEvent;
use App\Module\SharedContext\Domain\ValueObject\Roles;
use App\Module\SharedContext\Domain\ValueObject\UserId;

final readonly class UserAccountRolesSynced extends DomainEvent
{
    public function __construct(
        private UserId $aggregateId,
        private Roles $roles,
    ) {
        parent::__construct($this->aggregateId);
    }

    public static function eventName(): string
    {
        return 'auth.user_account.roles_synced';
    }

    public function userId(): UserId
    {
        return $this->aggregateId;
    }

    public function roles(): Roles
    {
        return $this->roles;
    }
}
