<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Event;

use App\Module\CostManagement\Domain\Enum\CostItemStatus;
use App\Module\CostManagement\Domain\Enum\CostItemType;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\CostManagement\Domain\ValueObject\CostItemName;
use App\Module\CostManagement\Domain\ValueObject\CoveragePeriod;
use App\Module\SharedContext\Domain\ValueObject\Money;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\DomainEventInterface;

/**
 * Événement émis lorsqu'un nouveau poste de coût est créé avec succès.
 */
final class CostItemAdded extends DomainEvent implements DomainEventInterface
{
    public function __construct(
        private readonly CostItemId $costItemId,
        private readonly CostItemName $name,
        private readonly CostItemType $type,
        private readonly Money $targetAmount,
        private readonly CoveragePeriod $coveragePeriod,
        private readonly CostItemStatus $status,
    ) {
        parent::__construct();
    }

    public function costItemId(): CostItemId
    {
        return $this->costItemId;
    }

    public function name(): CostItemName
    {
        return $this->name;
    }

    public function type(): CostItemType
    {
        return $this->type;
    }

    public function targetAmount(): Money
    {
        return $this->targetAmount;
    }

    public function coveragePeriod(): CoveragePeriod
    {
        return $this->coveragePeriod;
    }

    public function status(): CostItemStatus
    {
        return $this->status;
    }
}
