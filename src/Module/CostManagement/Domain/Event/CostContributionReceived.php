<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Event;

use App\Module\CostManagement\Domain\ValueObject\CostContributionId;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\SharedContext\Domain\ValueObject\Money;
use App\Module\SharedContext\Domain\ValueObject\ProductId;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\DomainEventInterface;

/**
 * Événement émis chaque fois qu'une contribution financière est ajoutée à un poste de coût.
 */
final class CostContributionReceived extends DomainEvent implements DomainEventInterface
{
    public function __construct(
        private readonly CostItemId $costItemId,
        private readonly CostContributionId $costContributionId,
        private readonly Money $contributionAmount,
        private readonly Money $newTotalCoveredAmount,
        public readonly ?ProductId $sourceProductId = null,
    ) {
        parent::__construct();
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
