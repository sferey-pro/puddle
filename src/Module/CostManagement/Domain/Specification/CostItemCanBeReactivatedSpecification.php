<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification;

use App\Core\Specification\AbstractSpecification;
use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\Enum\CostItemStatus;

/**
 * Spécification qui vérifie si un CostItem peut être réactivé.
 *
 * Un item peut être réactivé seulement s'il est actuellement archivé
 * ET que sa période de couverture n'est pas encore terminée.
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
