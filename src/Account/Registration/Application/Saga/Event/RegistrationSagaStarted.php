<?php

declare(strict_types=1);

namespace Account\Registration\Application\Saga\Event;

use Kernel\Domain\Event\DomainEvent;
use Kernel\Domain\Saga\SagaStateId;

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
