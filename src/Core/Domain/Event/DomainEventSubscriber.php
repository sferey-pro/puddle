<?php

declare(strict_types=1);

namespace App\Core\Domain\Event;

/**
 * @todo
 */
interface DomainEventSubscriber
{
    public static function subscribedTo(): array;
}
