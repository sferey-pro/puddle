<?php

namespace App\Module\CostManagement\Domain\Event;

use App\Module\CostManagement\Domain\ValueObject\CostContributionId;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\SharedContext\Domain\ValueObject\Money;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\DomainEventInterface;

/**
 * Événement de domaine levé lorsqu'une contribution à un poste de coût
 * a été annulée avec succès.
 */
final class CostContributionCancelled extends DomainEvent implements DomainEventInterface
{
    public function __construct(
        public readonly CostItemId $costItemId,
        public readonly CostContributionId $contributionId,
        public readonly Money $newTotalCovered,
        ?string $eventId = null,
        ?string $occurredOn = null
    ) {
        parent::__construct($costItemId->value, $eventId, $occurredOn);
    }
}
