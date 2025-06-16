<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Event;

use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\DomainEventInterface;

final class UserDeleted extends DomainEvent implements DomainEventInterface
{
    public function __construct(
        private readonly UserId $userId,
    ) {
        parent::__construct();
    }

    public static function eventName(): string
    {
        return 'usermanagement.user.deleted';
    }

    public function userId(): UserId
    {
        return $this->userId;
    }
}
