<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification;

use App\Core\Specification\AbstractSpecification;
use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\Enum\CostItemStatus;
use App\Shared\Domain\Service\ClockInterface;

/**
 * Spécification qui vérifie si un CostItem est considéré comme actif.
 *
 * Un item est actif si son statut est 'ACTIVE' et que la date actuelle
 * se trouve à l'intérieur de sa période de couverture.
 *
 * @template-extends AbstractSpecification<CostItem>
 */
final class CostItemIsActiveSpecification extends AbstractSpecification
{
    /**
     * @param CostItem $candidate
     */
    public function isSatisfiedBy($candidate): bool
    {
        $statusIsActive = $candidate->status()->equals(CostItemStatus::ACTIVE);

        $coveragePeriodIsActive = (new CoveragePeriodIsActiveSpecification())
            ->isSatisfiedBy($candidate->coveragePeriod());

        return $statusIsActive && $coveragePeriodIsActive;
    }
}
