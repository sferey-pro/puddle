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

    /**
     * Reconstitue un agrégat à partir de ses événements.
     * Utilisé pour l'Event Sourcing pur.
     */
    public static function reconstitute(string $id, array $events): static;
}
