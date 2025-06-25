<?php

declare(strict_types=1);

namespace App\Core\Domain\Event;

use App\Core\Application\Clock\SystemTime;
use App\Core\Domain\ValueObject\AggregateRootId;
use App\Core\Domain\ValueObject\EventId;

abstract readonly class DomainEvent implements DomainEventInterface
{
    private EventId $eventId;
    private AggregateRootId $aggregateId;
    private \DateTimeImmutable $occurredOn;

    public function __construct(AggregateRootId $aggregateId)
    {
        $this->eventId = EventId::generate();
        $this->aggregateId = $aggregateId;
        $this->occurredOn = SystemTime::now();
    }

    final public function eventId(): EventId
    {
        return $this->eventId;
    }

    final public function aggregateId(): AggregateRootId
    {
        return $this->aggregateId;
    }

    final public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }

    abstract public static function eventName(): string;
}
