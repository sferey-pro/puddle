<?php

declare(strict_types=1);

namespace Account\Registration\Application\Event;

use SharedKernel\Domain\ValueObject\Identity\UserId;
use Kernel\Application\Message\EventInterface;
use Kernel\Domain\ValueObject\EventId;

final readonly class RegistrationProcessCompleted implements EventInterface
{
    private EventId $eventId;

    public function __construct(
        public UserId $userId,
        private \DateTimeImmutable $occurredOn
    ) {
        $this->eventId = EventId::generate();
    }

    public function eventId(): EventId
    {
        return $this->eventId;
    }

    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
