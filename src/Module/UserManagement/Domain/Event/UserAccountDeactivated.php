<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Event;

use App\Core\Domain\Event\DomainEvent;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Événement levé lorsqu'un compte utilisateur est désactivé.
 */
final readonly class UserAccountDeactivated extends DomainEvent
{
    public function __construct(
        private UserId $userId,
        private ?string $reason = null,
    ) {
        parent::__construct($this->userId);
    }

    public static function eventName(): string
    {
        return 'user_management.user.account_deactivated';
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function reason(): ?string
    {
        return $this->reason;
    }
}
