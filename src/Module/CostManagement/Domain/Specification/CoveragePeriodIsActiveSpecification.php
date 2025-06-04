<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification;

use App\Core\Specification\AbstractSpecification;
use App\Module\CostManagement\Domain\ValueObject\CoveragePeriod;

/**
 * @extends AbstractSpecification<CoveragePeriod>
 *
 * Specifies that a CoveragePeriod is currently active.
 * A period is active if the current date is on or after the start date
 * AND on or before the end date.
 */
final class CoveragePeriodIsActiveSpecification extends AbstractSpecification
{
    public function __construct(private readonly \DateTimeImmutable $currentDate)
    {
    }

    /**
     * @param CoveragePeriod $candidate
     */
    public function isSatisfiedBy($candidate): bool
    {
        if (null === $candidate->getEndDate()) {
            return $this->currentDate >= $candidate->getStartDate();
        }

        return $this->currentDate >= $candidate->getStartDate() && $this->currentDate <= $candidate->getEndDate();
    }
}
