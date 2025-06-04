<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification;

use App\Core\Specification\AbstractSpecification;
use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\Enum\CostItemStatus;

/**
 * @extends AbstractSpecification<CostItem>
 *
 * Specifies that a CostItem is currently active.
 * This means its status is ACTIVE and its coverage period is currently active.
 */
final class CostItemIsActiveSpecification extends AbstractSpecification
{
    public function __construct(
        private readonly \DateTimeImmutable $currentDate = new \DateTimeImmutable(),
    ) {
    }

    /**
     * @param CostItem $candidate
     */
    public function isSatisfiedBy($candidate): bool
    {
        $statusIsActive = $candidate->status()->equals(CostItemStatus::ACTIVE);
        $coveragePeriodIsActive = (new CoveragePeriodIsActiveSpecification($this->currentDate))
            ->isSatisfiedBy($candidate->coveragePeriod());

        return $statusIsActive && $coveragePeriodIsActive;
    }
}
