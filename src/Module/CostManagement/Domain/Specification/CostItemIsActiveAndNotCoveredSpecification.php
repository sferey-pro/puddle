<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification\Composite;

use App\Core\Specification\AndSpecification;
use App\Core\Specification\NotSpecification;
use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\Specification\CostItemIsActiveSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemIsFullyCoveredSpecification;

/**
 * Specifies that a CostItem is active AND not fully covered.
 * This is a composite specification.
 */
final class CostItemIsActiveAndNotCoveredSpecification extends AndSpecification
{
    public function __construct()
    {
        // A CostItem is considered "active and not covered" if:
        // 1. It is active (checked by CostItemIsActiveSpecification)
        // AND
        // 2. It is NOT fully covered (checked by the negation of CostItemIsFullyCoveredSpecification)
        parent::__construct(
            new CostItemIsActiveSpecification(),
            new NotSpecification(new CostItemIsFullyCoveredSpecification())
        );
    }

    /**
     * Checks if the given CostItem satisfies the specification.
     *
     * @param CostItem $candidate The CostItem to check.
     */
    public function isSatisfiedBy($candidate): bool
    {
        return parent::isSatisfiedBy($candidate);
    }
}
