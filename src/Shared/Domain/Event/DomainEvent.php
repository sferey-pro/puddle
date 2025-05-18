<?php

declare(strict_types=1);

namespace App\Shared\Domain\Event;

use App\Shared\Domain\ValueObject\EventId;
use Symfony\Contracts\EventDispatcher\Event;

abstract class DomainEvent extends Event
{
    public readonly EventId $id;
    public readonly \DateTimeImmutable $occurredOn;

    public function __construct()
    {
        $this->id = EventId::random();
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
