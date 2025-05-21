<?php

declare(strict_types=1);

namespace App\Shared\Application\Event;

use App\Shared\Domain\Event\DomainEventInterface;

/**
 * Définit le contrat pour un bus d'événements applicatif.
 * Les implémentations se chargeront du dispatching effectif des événements.
 */
interface EventBusInterface
{
	public function publish(DomainEventInterface ...$events): void;
}
