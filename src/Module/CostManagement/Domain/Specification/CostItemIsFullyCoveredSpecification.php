<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification;

use App\Core\Specification\AbstractSpecification;
use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\Enum\CostItemStatus;

/**
 * @extends AbstractSpecification<CostItem>
 *
 * Specifies that a CostItem has its current amount fully covered
 * in relation to its target amount.
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
