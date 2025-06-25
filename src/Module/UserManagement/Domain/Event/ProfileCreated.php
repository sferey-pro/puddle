<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Event;

use App\Core\Domain\Event\DomainEvent;
use App\Module\SharedContext\Domain\ValueObject\UserId;

final readonly class ProfileCreated extends DomainEvent
{
    public function __construct(
        private UserId $userId,
    ) {
        parent::__construct($this->userId);
    }

    public static function eventName(): string
    {
        return 'user_management.profile.created';
    }

    public function userId(): UserId
    {
        return $this->userId;
    }
}
