<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification\Composite;

use App\Core\Specification\AndSpecification;
use App\Core\Specification\NotSpecification;
use App\Module\CostManagement\Domain\CostItem;

/**
 * Spécification composite qui vérifie si un CostItem est à la fois actif ET non couvert.
 *
 * Utilise une combinaison de `CostItemIsActiveSpecification` et de la négation (`NotSpecification`)
 * de `CostItemIsFullyCoveredSpecification` pour exprimer cette règle métier.
 *
 * @template-extends AndSpecification<CostItem>
 */
final class CostItemIsActiveAndNotCoveredSpecification extends AndSpecification
{
    /**
     * Construit la spécification.
     * Un CostItem est considéré "actif et non couvert" si :
     * 1. Il est actif (vérifié par CostItemIsActiveSpecification)
     * ET
     * 2. Il N'EST PAS entièrement couvert (vérifié par la négation de CostItemIsFullyCoveredSpecification).
     */
    public function __construct()
    {
        parent::__construct(
            new CostItemIsActiveSpecification(),
            new NotSpecification(new CostItemIsFullyCoveredSpecification())
        );
    }
}
