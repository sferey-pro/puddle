<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification;

use App\Core\Application\Clock\SystemTime;
use App\Core\Specification\AbstractSpecification;
use App\Module\CostManagement\Domain\ValueObject\CoveragePeriod;

/**
 * Spécification qui détermine si une `CoveragePeriod` est terminée.
 *
 * Une période est considérée comme terminée si la date actuelle est
 * strictement postérieure à la date de fin de la période. Si la date de fin
 * est nulle, la période n'est jamais considérée comme terminée.
 *
 * @template-extends AbstractSpecification<CoveragePeriod>
 */
final class CoveragePeriodHasEndedSpecification extends AbstractSpecification
{
    /**
     * @param CoveragePeriod $candidate
     */
    public function isSatisfiedBy($candidate): bool
    {
        if (null === $candidate->getEndDate()) {
            return false; // Une période sans date de fin explicite n'est pas "terminée"
        }

        return SystemTime::now() > $candidate->getEndDate();
    }
}
