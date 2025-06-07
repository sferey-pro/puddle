<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification;

use App\Core\Specification\AbstractSpecification;
use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\Enum\CostItemStatus;
use App\Module\SharedContext\Domain\ValueObject\Money;

/**
 * Spécification qui vérifie si un CostItem est entièrement couvert.
 *
 * La condition est remplie si le statut est déjà `FULLY_COVERED`, ou si le
 * montant actuellement couvert est supérieur ou égal au montant cible.
 *
 * @template-extends AbstractSpecification<CostItem>
 */
final class CostItemIsFullyCoveredSpecification extends AbstractSpecification
{
    /**
     * @param CostItem $candidate
     */
    public function isSatisfiedBy($candidate): bool
    {
        /** @var CostItem $candidate */
        $targetAmount = $candidate->targetAmount();

        if ($targetAmount->isEqualTo(Money::zero())) {
            $this->setFailureReason('Le montant cible est de zéro ou moins, le concept de "couverture" ne s\'applique pas.');
            return false;
        }

        // Soit le statut est déjà FULLY_COVERED
        if ($candidate->status()->equals(CostItemStatus::FULLY_COVERED)) {
            return true;
        }

        // Soit le montant couvert atteint ou dépasse la cible
        return $candidate->currentAmountCovered()->isGreaterThanOrEqual($candidate->targetAmount());
    }
}
