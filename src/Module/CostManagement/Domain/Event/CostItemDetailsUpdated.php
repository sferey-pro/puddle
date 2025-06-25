<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Event;

use App\Core\Domain\Event\DomainEvent;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\CostManagement\Domain\ValueObject\CostItemName;
use App\Module\CostManagement\Domain\ValueObject\CoveragePeriod;
use App\Module\SharedContext\Domain\ValueObject\Money;

/**
 * Événement émis lorsque les détails principaux d'un poste de coût sont mis à jour.
 */
final readonly class CostItemDetailsUpdated extends DomainEvent
{
    public function __construct(
        private CostItemId $costItemId,
        private CostItemName $newName,
        private CostItemName $oldName,
        private Money $newTargetAmount,
        private Money $oldTargetAmount,
        private CoveragePeriod $newCoveragePeriod,
        private CoveragePeriod $oldCoveragePeriod,
        private ?string $newDescription,
        private ?string $oldDescription,
    ) {
        parent::__construct($this->costItemId);
    }

    public static function eventName(): string
    {
        return 'cost_management.costitem.details_updated';
    }

    public function costItemId(): CostItemId
    {
        return $this->costItemId;
    }

    public function newName(): CostItemName
    {
        return $this->newName;
    }

    public function oldName(): CostItemName
    {
        return $this->oldName;
    }

    public function newTargetAmount(): Money
    {
        return $this->newTargetAmount;
    }

    public function oldTargetAmount(): Money
    {
        return $this->oldTargetAmount;
    }

    public function newCoveragePeriod(): CoveragePeriod
    {
        return $this->newCoveragePeriod;
    }

    public function oldCoveragePeriod(): CoveragePeriod
    {
        return $this->oldCoveragePeriod;
    }

    public function newDescription(): ?string
    {
        return $this->newDescription;
    }

    public function oldDescription(): ?string
    {
        return $this->oldDescription;
    }
}
