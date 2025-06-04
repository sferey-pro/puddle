<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification\Composite;

use App\Core\Specification\AbstractSpecification;
use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\Specification\CostItemHasZeroTargetSpecification;
use App\Module\CostManagement\Domain\Specification\CoveragePeriodHasEndedSpecification;

/**
 * Spécification qui vérifie si un CostItem remplit les conditions pour être archivé.
 *
 * Cette règle métier est satisfaite si :
 * 1. La période de couverture du poste de coût est terminée.
 * OU
 * 2. Le poste est encore actif mais n'est pas entièrement couvert (permettant un archivage anticipé, comme une annulation).
 * OU
 * 3. Le coût cible est 0, il n'y a pas d'objectif financier.
 *
 * Note : Cette spécification ne vérifie pas si l'item est *déjà* archivé.
 * Cette vérification est de la responsabilité de l'agrégat (CostItem) avant d'appliquer cette spécification,
 * afin de séparer les vérifications d'état des vérifications de règles métier.
 *
 * @template-extends AbstractSpecification<CostItem>
 */
final class CostItemCanBeArchivedSpecification extends AbstractSpecification
{
    /**
     * @param CostItem $candidate L'objet CostItem à évaluer
     */
    public function isSatisfiedBy($candidate): bool
    {
        // Règle 1: La période de couverture est terminée.
        $coveragePeriodHasEnded = (new CoveragePeriodHasEndedSpecification())
            ->isSatisfiedBy($candidate->coveragePeriod());

        // Règle 2: L'item est actif mais pas encore couvert.
        $isActiveAndNotCovered = (new CostItemIsActiveAndNotCoveredSpecification())
            ->isSatisfiedBy($candidate);

        // Règle 3: Le coût cible est 0, il n'y a pas d'objectif financier
        $hasZeroTarget = (new CostItemHasZeroTargetSpecification())
            ->isSatisfiedBy($candidate);

        // Un CostItem peut être archivé si l'une ou l'autre de ces conditions est remplie.
        return $coveragePeriodHasEnded || $isActiveAndNotCovered || $hasZeroTarget;
    }
}
