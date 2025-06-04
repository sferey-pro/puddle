<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification;

use App\Core\Specification\AbstractSpecification;
use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\Specification\Composite\CostItemIsActiveAndNotCoveredSpecification;

/**
 * @extends AbstractSpecification<CostItem>
 *
 * Specifies that a CostItem can receive a monetary contribution.
 * This is true if the CostItem is active and not yet fully covered.
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
