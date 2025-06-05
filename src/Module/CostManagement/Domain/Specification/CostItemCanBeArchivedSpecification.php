<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification;

use App\Core\Specification\AbstractSpecification;
use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\Specification\Composite\CostItemIsActiveAndNotCoveredSpecification;

/**
 * Spécification qui vérifie si un CostItem peut être archivé.
 *
 * Un poste de coût peut être archivé s'il n'est pas déjà archivé ET
 * (il est actif mais non couvert OU sa période de couverture est terminée).
 */
final class CostItemCanBeArchivedSpecification extends AbstractSpecification
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
        // It can be archived if it's active and not covered (meaning it's an ongoing cost that needs to be stopped)
        $isActiveAndNotCovered = (new CostItemIsActiveAndNotCoveredSpecification())->isSatisfiedBy($candidate);

        // Or it can be archived if its coverage period has simply ended (even if covered)
        $coveragePeriodHasEnded = (new CoveragePeriodHasEndedSpecification($this->currentDate))->isSatisfiedBy($candidate->coveragePeriod());

        return $isActiveAndNotCovered || $coveragePeriodHasEnded;
    }
}
