<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification;

use App\Core\Specification\AbstractSpecification;
use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\Enum\CostItemStatus;

/**
 * @extends AbstractSpecification<CostItem>
 *
 * Specifies that a CostItem can be reactivated.
 * A CostItem can be reactivated if it is currently archived
 * and its coverage period has not ended yet.
 */
final class CostItemCanBeReactivatedSpecification extends AbstractSpecification
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
        $isArchived = $candidate->status()->equals(CostItemStatus::ARCHIVED);
        $coveragePeriodNotEnded = !(new CoveragePeriodHasEndedSpecification($this->currentDate))->isSatisfiedBy($candidate->coveragePeriod());

        return $isArchived && $coveragePeriodNotEnded;
    }
}
