<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification;

use App\Core\Specification\AbstractSpecification;
use App\Module\CostManagement\Domain\CostItem;
use App\Module\SharedContext\Domain\ValueObject\Money;

/**
 * @extends AbstractSpecification<CostItem>
 *
 * Specifies that the target amount of a CostItem can be safely updated.
 * An update is safe if the new target amount is not less than the currently covered amount.
 * This prevents scenarios where a cost item could become "over-covered" by reducing its target.
 */
final class CostItemTargetCanBeSafelyUpdatedSpecification extends AbstractSpecification
{
    public function __construct(private readonly Money $newTargetAmount)
    {
    }

    /**
     * @param CostItem $candidate
     */
    public function isSatisfiedBy($candidate): bool
    {
        // The new target amount must be greater than or equal to the current amount.
        return $this->newTargetAmount->isGreaterThanOrEqual($candidate->currentAmountCovered());
    }
}
