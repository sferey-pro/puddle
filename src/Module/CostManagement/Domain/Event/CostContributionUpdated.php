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
 * Événement émis lorsqu'une contribution existante a été mise à jour.
 */
final class CostContributionUpdated extends DomainEvent implements DomainEventInterface
{
    public function __construct(
        private readonly CostItemId $costItemId,
        private readonly CostContributionId $costContributionId,
        private readonly Money $newContributionAmount,
        private readonly Money $newTotalCoveredAmount,
        private readonly ?ProductId $newSourceProductId = null,
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
