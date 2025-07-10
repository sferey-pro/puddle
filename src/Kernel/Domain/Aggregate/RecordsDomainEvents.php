<?php

declare(strict_types=1);

namespace Kernel\Domain\Aggregate;

/**
 * Trait pour les Aggregates qui enregistrent des Domain Events.
 * Les événements sont stockés en interne jusqu'à ce qu'ils soient explicitement récupérés.
 */
trait RecordsDomainEvents
{
    /**
     * @var list<object> La liste des événements de domaine enregistrés.
     */
    private array $recordedEvents = [];

    /**
     * Récupère tous les événements de domaine enregistrés et vide la liste.
     * C'est la méthode que l'infrastructure (Repository, Event Dispatcher) appellera.
     *
     * @return list<object>
     */
    final public function pullDomainEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];

        return $events;
    }

    /**
     * Vide la liste des événements de domaine enregistrés.
     * @return void
     */
    public function eraseRecordedDomainEvent(): void
    {
        $this->recordedEvents = [];
    }

    /**
     * Enregistre un événement de domaine.
     * Cette méthode est protégée pour s'assurer que seuls les Aggregates
     * eux-mêmes peuvent lever des événements.
     */
    final protected function raise(object $event): void
    {
        $this->recordedEvents[] = $event;
    }
}
