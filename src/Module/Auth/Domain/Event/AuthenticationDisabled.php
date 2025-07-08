<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Event;

use App\Core\Domain\Event\DomainEvent;
use App\Module\SharedContext\Domain\ValueObject\UserId;

final readonly class AuthenticationDisabled extends DomainEvent
{
    public function __construct(
        private UserId $aggregateId,
        private string $reason,
    ) {
        parent::__construct($aggregateId);
    }

    public static function eventName(): string
    {
        return 'auth.authentication.disabled';
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
