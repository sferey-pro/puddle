<?php

namespace Kernel\Domain\Contract\Aggregate;

use Kernel\Domain\Event\DomainEvent;

interface EventSourced
{
    /**
     * @return DomainEvent[]
     */
    public function getRecordedEvents(): array;
    public function markEventsAsCommitted(): void;
    public static function reconstitute(string $id, array $events): static;
}