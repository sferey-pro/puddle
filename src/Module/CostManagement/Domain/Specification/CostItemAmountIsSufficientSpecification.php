<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification;

use App\Core\Specification\AbstractSpecification;
use App\Module\CostManagement\Domain\CostItem;

/**
 * Vérifie si le montant couvert d'un CostItem atteint ou dépasse sa cible.
 */
final class CostItemAmountIsSufficientSpecification extends AbstractSpecification
{
    /**
     * @param CostItem $candidate
     */
    public function isSatisfiedBy($candidate): bool
    {
        return $candidate->currentAmountCovered()->isGreaterThanOrEqual($candidate->targetAmount());
    }
}
