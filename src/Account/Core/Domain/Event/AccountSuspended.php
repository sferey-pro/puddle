<?php

declare(strict_types=1);

namespace Account\Core\Domain\Event;

use SharedKernel\Domain\ValueObject\Identity\UserId;
use Kernel\Domain\Event\DomainEvent;

final readonly class AccountSuspended extends DomainEvent
{
    public function __construct(
        private UserId $aggregateId,
        private string $reason,
    ) {
        parent::__construct($this->aggregateId);
    }

    public static function eventName(): string
    {
        return 'account.suspended';
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
