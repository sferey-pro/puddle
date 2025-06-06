<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification;

use App\Core\Specification\AbstractSpecification;
use App\Module\CostManagement\Domain\ValueObject\CoveragePeriod;

/**
 * Spécification qui détermine si une `CoveragePeriod` est actuellement active.
 *
 * Une période est active si la date actuelle se situe entre la date de début (incluse)
 * et la date de fin (incluse). Si la date de fin est nulle, elle est active si
 * la date actuelle est postérieure ou égale à la date de début.
 *
 * @template-extends AbstractSpecification<CoveragePeriod>
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
