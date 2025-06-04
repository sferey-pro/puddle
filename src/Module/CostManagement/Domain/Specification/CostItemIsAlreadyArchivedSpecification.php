<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification;

use App\Core\Specification\AbstractSpecification;
use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\Enum\CostItemStatus;

/**
 * @extends AbstractSpecification<CostItem>
 *
 * Specifies that a CostItem is already in an ARCHIVED status.
 */
final class CostItemIsAlreadyArchivedSpecification extends AbstractSpecification
{
    /**
     * @param CostItem $candidate
     */
    public function isSatisfiedBy($candidate): bool
    {
        return $candidate->status()->equals(CostItemStatus::ARCHIVED);
    }
}
