<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification\Composite;

use App\Core\Specification\AbstractSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemIsArchivedSpecification;
use App\Module\CostManagement\Domain\Specification\CoveragePeriodHasEndedSpecification;

/**
 * Spécification pour vérifier qu'un poste de coût peut être réactivé.
 *
 * Règles :
 * 1. Le statut actuel DOIT être 'ARCHIVED'.
 * 2. La période de couverture NE DOIT PAS être déjà terminée.
 */
class CostItemCanBeReactivatedSpecification extends AbstractSpecification
{
    /**
     * @param CostItem $candidate L'objet CostItem à évaluer
     */
    public function isSatisfiedBy($candidate): bool
    {
        $costItemIsArchived = new CostItemIsArchivedSpecification()
            ->isSatisfiedBy($candidate);

        $coveragePeriodHasEnded = new CoveragePeriodHasEndedSpecification()
                ->isSatisfiedBy($candidate->coveragePeriod())
        ;

        return $costItemIsArchived && !$coveragePeriodHasEnded;
    }
}
