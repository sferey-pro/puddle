<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification;

use App\Core\Specification\AbstractSpecification;
use App\Module\CostManagement\Domain\CostItem;

/**
 * Spécification qui vérifie si un CostItem remplit les conditions pour être archivé.
 *
 * Cette règle métier est satisfaite si :
 * 1. La période de couverture du poste de coût est terminée.
 * OU
 * 2. Le poste est encore actif mais n'est pas entièrement couvert (permettant un archivage anticipé, comme une annulation).
 *
 * Note : Cette spécification ne vérifie pas si l'item est *déjà* archivé.
 * Cette vérification est de la responsabilité de l'agrégat (CostItem) avant d'appliquer cette spécification,
 * afin de séparer les vérifications d'état des vérifications de règles métier.
 *
 * @template-extends AbstractSpecification<CostItem>
 */
final class CostItemCanBeArchivedSpecification extends AbstractSpecification
{
    public function __construct(
        private readonly \DateTimeImmutable $currentDate = new \DateTimeImmutable(),
    ) {
    }

    /**
     * @param CostItem $candidate L'objet CostItem à évaluer.
     */
    public function isSatisfiedBy($candidate): bool
    {
        // Règle 1: La période de couverture est terminée.
        // On instancie la spécification adéquate et on lui passe la propriété pertinente de notre candidat.
        $coveragePeriodHasEnded = (new CoveragePeriodHasEndedSpecification($this->currentDate))
            ->isSatisfiedBy($candidate->coveragePeriod());

        // Règle 2: L'item est actif mais pas encore couvert.
        // On utilise ici la spécification composite qui attend l'objet CostItem en entier.
        $isActiveAndNotCovered = (new Composite\CostItemIsActiveAndNotCoveredSpecification())
            ->isSatisfiedBy($candidate);

        // Un CostItem peut être archivé si l'une ou l'autre de ces conditions est remplie.
        return $coveragePeriodHasEnded || $isActiveAndNotCovered;
    }
}
