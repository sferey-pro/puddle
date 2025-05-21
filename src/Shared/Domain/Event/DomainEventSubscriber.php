<?php

declare(strict_types=1);

namespace App\Shared\Domain\Event;

/**
 * @todo
 * @package App\Shared\Domain\Event
 */
interface DomainEventSubscriber
{
	public static function subscribedTo(): array;
}
