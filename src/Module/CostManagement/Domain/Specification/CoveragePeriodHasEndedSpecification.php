<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification;

use App\Core\Specification\AbstractSpecification;
use App\Module\CostManagement\Domain\ValueObject\CoveragePeriod;

/**
 * @extends AbstractSpecification<CoveragePeriod>
 *
 * Specifies that a CoveragePeriod has ended based on the current date.
 * The period is considered ended if the current date is past the period's end date.
 */
final class CoveragePeriodHasEndedSpecification extends AbstractSpecification
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
            return false; // Une période sans date de fin explicite n'est pas "terminée"
        }

        return $this->currentDate > $candidate->getEndDate();
    }
}
