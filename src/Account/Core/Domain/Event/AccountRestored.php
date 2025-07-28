<?php

declare(strict_types=1);

namespace Account\Core\Domain\Event;

use Kernel\Domain\Event\DomainEvent;
use SharedKernel\Domain\ValueObject\Identity\UserId;

final readonly class AccountRestored extends DomainEvent
{
    public function __construct(
        private UserId $aggregateId,
    ) {
        parent::__construct($aggregateId);
    }

    public static function eventName(): string
    {
        return 'account.restored';
    }

    public function userId(): UserId
    {
        return $this->aggregateId;
    }
}
