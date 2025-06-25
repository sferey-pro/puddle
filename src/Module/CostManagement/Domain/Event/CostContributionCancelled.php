<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Event;

use App\Core\Domain\Event\DomainEvent;
use App\Module\CostManagement\Domain\ValueObject\CostContributionId;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\SharedContext\Domain\ValueObject\Money;

/**
 * Événement de domaine levé lorsqu'une contribution à un poste de coût
 * a été annulée avec succès.
 */
final readonly class CostContributionCancelled extends DomainEvent
{
    public function __construct(
        private CostItemId $costItemId,
        private CostContributionId $contributionId,
        private Money $newTotalCovered,
    ) {
        parent::__construct($this->costItemId);
    }

    public static function eventName(): string
    {
        return 'cost_management.costitem.contribution_cancelled';
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
