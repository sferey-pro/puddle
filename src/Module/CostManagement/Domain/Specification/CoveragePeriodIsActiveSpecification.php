<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification;

use App\Core\Specification\AbstractSpecification;
use App\Module\CostManagement\Domain\ValueObject\CoveragePeriod;
use App\Shared\Domain\Service\SystemTime;

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
    /**
     * @param CoveragePeriod $candidate
     */
    public function isSatisfiedBy($candidate): bool
    {
        if (null === $candidate->getEndDate()) {
            return SystemTime::now() >= $candidate->getStartDate();
        }

        return SystemTime::now() >= $candidate->getStartDate() && SystemTime::now() <= $candidate->getEndDate();
    }
}
