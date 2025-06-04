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
        private readonly CostItemId $costItemId,
        private readonly CostContributionId $contributionId,
        private readonly Money $newTotalCovered,
    ) {
        parent::__construct();
    }

    public function costItemId(): CostItemId
    {
        return $this->costItemId;
    }

    public function contributionId(): CostContributionId
    {
        return $this->contributionId;
    }

    public function newTotalCovered(): Money
    {
        return $this->newTotalCovered;
    }
}
