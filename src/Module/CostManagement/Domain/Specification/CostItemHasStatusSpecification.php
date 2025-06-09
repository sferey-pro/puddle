<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification;

use App\Core\Specification\AbstractSpecification;
use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\Enum\CostItemStatus;

/**
 * Spécification qui vérifie si un CostItem a un statut spécifique.
 *
 * @template-extends AbstractSpecification<CostItem>
 */
class CostItemHasStatusSpecification extends AbstractSpecification
{
    public function __construct(private readonly CostItemStatus $expectedStatus)
    {
    }

    /**
     * @param CostItem $candidate
     */
    public function isSatisfiedBy($candidate): bool
    {
        return $candidate->status()->equals($this->expectedStatus);
    }
}
