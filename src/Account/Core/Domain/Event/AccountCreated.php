<?php

declare(strict_types=1);

namespace Account\Core\Domain\Event;

use Identity\Domain\ValueObject\Identifier;
use Kernel\Domain\Event\DomainEvent;
use SharedKernel\Domain\ValueObject\Identity\UserId;

final readonly class AccountCreated extends DomainEvent
{
    public function __construct(
        private UserId $aggregateId,
        private Identifier $identifier,
    ) {
        parent::__construct($this->aggregateId);
    }

    public static function eventName(): string
    {
        return 'account.created';
    }

    public function userId(): UserId
    {
        return $this->aggregateId;
    }

    public function identifier(): Identifier
    {
        return $this->identifier;
    }
}
