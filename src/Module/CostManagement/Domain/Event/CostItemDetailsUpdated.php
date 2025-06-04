<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Event;

use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\CostManagement\Domain\ValueObject\CostItemName;
use App\Module\CostManagement\Domain\ValueObject\CoveragePeriod;
use App\Module\SharedContext\Domain\ValueObject\Money;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\DomainEventInterface;

/**
 * Événement émis lorsque les détails principaux d'un poste de coût sont mis à jour.
 */
final class CostItemDetailsUpdated extends DomainEvent implements DomainEventInterface
{
    public function __construct(
        private readonly CostItemId $costItemId,
        private readonly CostItemName $newName,
        private readonly CostItemName $oldName,
        private readonly Money $newTargetAmount,
        private readonly Money $oldTargetAmount,
        private readonly CoveragePeriod $newCoveragePeriod,
        private readonly CoveragePeriod $oldCoveragePeriod,
        private readonly ?string $newDescription,
        private readonly ?string $oldDescription,
    ) {
        parent::__construct();
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
