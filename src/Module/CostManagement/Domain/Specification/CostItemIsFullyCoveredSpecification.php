<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification;

use App\Core\Specification\AbstractSpecification;
use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\Enum\CostItemStatus;

/**
 * Spécification qui vérifie si un CostItem est entièrement couvert.
 *
 * La condition est remplie si le statut est déjà FULLY_COVERED, ou si le
 * montant actuel est supérieur ou égal au montant cible.
 */
final class CostItemIsFullyCoveredSpecification extends AbstractSpecification
{
    /**
     * @param CostItem $candidate
     */
    public function isSatisfiedBy($candidate): bool
    {
        // Soit le statut est déjà FULLY_COVERED
        if ($candidate->status()->equals(CostItemStatus::FULLY_COVERED)) {
            return true;
        }

        return $candidate->currentAmountCovered()->isGreaterThanOrEqual($candidate->targetAmount());
    }
}
