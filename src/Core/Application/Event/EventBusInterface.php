<?php

declare(strict_types=1);

namespace App\Core\Application\Event;

use App\Core\Domain\Event\DomainEventInterface;

/**
 * Définit le contrat pour un bus d'événements applicatif.
 * Les implémentations se chargeront du dispatching effectif des événements.
 */
interface EventBusInterface
{
    public function publish(DomainEventInterface ...$events): void;
}
