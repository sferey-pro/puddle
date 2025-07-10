<?php

declare(strict_types=1);

namespace Kernel\Domain\Event;

interface DomainEventSubscriber
{
    public static function subscribedTo(): array;
}
