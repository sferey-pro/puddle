<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Event;

use App\Core\Domain\Event\DomainEvent;
use App\Module\CostManagement\Domain\ValueObject\CostContributionId;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\SharedContext\Domain\ValueObject\Money;
use App\Module\SharedContext\Domain\ValueObject\ProductId;

/**
 * Événement émis chaque fois qu'une contribution financière est ajoutée à un poste de coût.
 */
final readonly class CostContributionReceived extends DomainEvent
{
    public function __construct(
        private CostItemId $costItemId,
        private CostContributionId $costContributionId,
        private Money $contributionAmount,
        private Money $newTotalCoveredAmount,
        private ?ProductId $sourceProductId = null,
    ) {
        parent::__construct($this->costItemId);
    }

    public static function eventName(): string
    {
        return 'cost_management.costitem.contribution_received';
    }

    public function costItemId(): CostItemId
    {
        return $this->costItemId;
    }

    public function costContributionId(): CostContributionId
    {
        return $this->costContributionId;
    }

    public function contributionAmount(): Money
    {
        return $this->contributionAmount;
    }

    public function newTotalCoveredAmount(): Money
    {
        return $this->newTotalCoveredAmount;
    }

    public function sourceProductId(): ?ProductId
    {
        return $this->sourceProductId;
    }
}
