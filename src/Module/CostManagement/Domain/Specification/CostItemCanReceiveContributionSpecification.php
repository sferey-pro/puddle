<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification;

use App\Core\Specification\AbstractSpecification;
use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\Specification\Composite\CostItemIsActiveAndNotCoveredSpecification;

/**
 * Spécification qui détermine si un CostItem peut recevoir une contribution financière.
 *
 * Cette règle est satisfaite si l'item est à la fois actif et pas encore entièrement couvert.
 * Elle délègue cette logique à la spécification composite `CostItemIsActiveAndNotCoveredSpecification`.
 *
 * @template-extends AbstractSpecification<CostItem>
 */
final class CostItemCanReceiveContributionSpecification extends AbstractSpecification
{
    /**
     * @param CostItem $candidate
     */
    public function isSatisfiedBy($candidate): bool
    {
        return (new CostItemIsActiveAndNotCoveredSpecification())->isSatisfiedBy($candidate);
    }
}
