<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Event;

use App\Core\Domain\Event\DomainEvent;
use App\Module\CostManagement\Domain\Enum\CostItemStatus;
use App\Module\CostManagement\Domain\Enum\CostItemType;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\CostManagement\Domain\ValueObject\CostItemName;
use App\Module\CostManagement\Domain\ValueObject\CoveragePeriod;
use App\Module\SharedContext\Domain\ValueObject\Money;

/**
 * Événement émis lorsqu'un nouveau poste de coût est créé avec succès.
 */
final readonly class CostItemAdded extends DomainEvent
{
    public function __construct(
        private CostItemId $costItemId,
        private CostItemName $name,
        private bool $isTemplate,
        private CostItemType $type,
        private Money $targetAmount,
        private CostItemStatus $status,
        private ?CoveragePeriod $coveragePeriod = null,
    ) {
        parent::__construct($this->costItemId);
    }

    public static function eventName(): string
    {
        return 'cost_management.costitem.added';
    }

    public function costItemId(): CostItemId
    {
        return $this->costItemId;
    }

    public function name(): CostItemName
    {
        return $this->name;
    }

    public function isTemplate(): bool
    {
        return $this->isTemplate;
    }

    public function type(): CostItemType
    {
        return $this->type;
    }

    public function targetAmount(): Money
    {
        return $this->targetAmount;
    }

    public function status(): CostItemStatus
    {
        return $this->status;
    }

    public function coveragePeriod(): ?CoveragePeriod
    {
        return $this->coveragePeriod;
    }
}
