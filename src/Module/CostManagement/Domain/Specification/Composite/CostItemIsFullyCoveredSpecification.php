<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification\Composite;

use App\Core\Specification\AbstractSpecification;
use App\Core\Specification\OrSpecification;
use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\Enum\CostItemStatus;
use App\Module\CostManagement\Domain\Specification\CostItemAmountIsSufficientSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemHasStatusSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemHasZeroTargetSpecification;
use App\Module\SharedContext\Domain\ValueObject\Money;

/**
 * Spécification qui vérifie si un CostItem est entièrement couvert.
 *
 * La condition est remplie si le statut est déjà `FULLY_COVERED`, ou si le
 * montant actuellement couvert est supérieur ou égal au montant cible.
 *
 * @template-extends OrSpecification<CostItem>
 */
final class CostItemIsFullyCoveredSpecification extends OrSpecification
{
    public function __construct()
    {
        parent::__construct(
            // Règle 1 : Le statut est déjà 'FULLY_COVERED'
            new CostItemHasStatusSpecification(CostItemStatus::FULLY_COVERED),

            // Règle 2 : OU le montant couvert est suffisant
            new CostItemAmountIsSufficientSpecification()
        );
    }

    /**
     * On surcharge la méthode pour ajouter notre garde métier :
     * le concept de "couverture" ne s'applique pas si la cible est zéro.
     * @param CostItem $candidate
     */
    public function isSatisfiedBy($candidate): bool
    {
        if ((new CostItemHasZeroTargetSpecification())->isSatisfiedBy($candidate)) {
            return false;
        }

        return parent::isSatisfiedBy($candidate);
    }
}
