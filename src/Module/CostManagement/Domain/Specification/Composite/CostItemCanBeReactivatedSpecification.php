<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification\Composite;

use App\Core\Specification\AbstractSpecification;
use App\Core\Specification\AndSpecification;
use App\Core\Specification\NotSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemIsArchivedSpecification;
use App\Module\CostManagement\Domain\Specification\CoveragePeriodHasEndedSpecification;

/**
 * Spécification pour vérifier qu'un poste de coût peut être réactivé.
 *
 * Règles :
 * 1. Le statut actuel DOIT être 'ARCHIVED'.
 * ET
 * 2. La période de couverture NE DOIT PAS être déjà terminée.
 *
 * @template-extends AndSpecification<CostItem>
 */
class CostItemCanBeReactivatedSpecification extends AndSpecification
{
    public function __construct()
    {
        parent::__construct(
            // Règle 1: Le statut est 'ARCHIVED'
            new CostItemIsArchivedSpecification(),

            // Règle 2: ET la période de couverture N'EST PAS terminée
            new NotSpecification(
                new class extends AbstractSpecification {
                    public function isSatisfiedBy($candidate): bool
                    {
                        /** @var CostItem $candidate */
                        return (new CoveragePeriodHasEndedSpecification())->isSatisfiedBy($candidate->coveragePeriod());
                    }
                }
            )
        );
    }
}
