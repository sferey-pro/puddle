<?php

declare(strict_types=1);

namespace App\Shared\Domain\Model;

use App\Shared\Domain\Event\DomainEventInterface;

trait DomainEventTrait
{
    private array $domainEvents = [];

    public function recordDomainEvent(DomainEventInterface $event): self
    {
        $this->domainEvents[] = $event;

        return $this;
    }

    public function eraseRecordedDomainEvent(): void
    {
        $this->domainEvents[] = [];
    }

    public function pullDomainEvents(): array
    {
        $domainEvents = $this->domainEvents;
        $this->domainEvents = [];

        return $domainEvents;
    }
}
