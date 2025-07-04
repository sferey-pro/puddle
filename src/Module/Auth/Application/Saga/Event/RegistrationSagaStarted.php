<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Saga\Event;

use App\Core\Domain\Event\DomainEvent;
use App\Core\Domain\Saga\SagaStateId;

/**
 * Événement signalant que le "Parcours Métier d'Inscription" a été initié.
 */
final readonly class RegistrationSagaStarted extends DomainEvent
{
    public function __construct(
        private SagaStateId $aggregateId,
    ) {
        parent::__construct($this->aggregateId);
    }

    public static function eventName(): string
    {
        return 'auth.registration.saga.started';
    }

    public function sagaStateId(): SagaStateId
    {
        return $this->aggregateId;
    }
}
