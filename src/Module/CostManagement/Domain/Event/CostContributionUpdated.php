<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Event;

use App\Core\Domain\Event\DomainEvent;
use App\Module\CostManagement\Domain\ValueObject\CostContributionId;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\SharedContext\Domain\ValueObject\Money;
use App\Module\SharedContext\Domain\ValueObject\ProductId;

/**
 * Événement émis lorsqu'une contribution existante a été mise à jour.
 */
final readonly class CostContributionUpdated extends DomainEvent
{
    public function __construct(
        private CostItemId $costItemId,
        private CostContributionId $costContributionId,
        private Money $newContributionAmount,
        private Money $newTotalCoveredAmount,
        private ?ProductId $newSourceProductId = null,
    ) {
        parent::__construct($this->costItemId);
    }

    public static function eventName(): string
    {
        return 'cost_management.costitem.contribution_updated';
    }

    public function costItemId(): CostItemId
    {
        return $this->costItemId;
    }

    public function costContributionId(): CostContributionId
    {
        return $this->costContributionId;
    }

    public function newContributionAmount(): Money
    {
        return $this->newContributionAmount;
    }

    public function newTotalCoveredAmount(): Money
    {
        return $this->newTotalCoveredAmount;
    }

    public function newSourceProductId(): ?ProductId
    {
        return $this->newSourceProductId;
    }
}
