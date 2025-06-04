<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification;

use App\Core\Specification\AbstractSpecification;
use App\Module\CostManagement\Domain\CostItem;
use App\Module\SharedContext\Domain\ValueObject\Money;

/**
 * Spécification qui vérifie si le montant cible d'un poste de coût est zéro.
 */
final class CostItemHasZeroTargetSpecification extends AbstractSpecification
{
    public function isSatisfiedBy(mixed $candidate): bool
    {
        /** @var CostItem $candidate */
        return $candidate->targetAmount()->isEqualTo(Money::zero());
    }
}
