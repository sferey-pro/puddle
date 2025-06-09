<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification\Composite;

use App\Core\Specification\AbstractSpecification;
use App\Core\Specification\AndSpecification;
use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\Enum\CostItemStatus;
use App\Module\CostManagement\Domain\Specification\CostItemHasStatusSpecification;
use App\Module\CostManagement\Domain\Specification\CoveragePeriodIsActiveSpecification;

/**
 * Spécification qui vérifie si un CostItem est considéré comme actif.
 *
 * Un item est actif si son statut est 'ACTIVE' et que la date actuelle
 * se trouve à l'intérieur de sa période de couverture.
 *
 * @template-extends AbstractSpecification<CostItem>
 */
final class CostItemIsActiveSpecification extends AndSpecification
{
    public function __construct()
    {
        // On passe les deux spécifications atomiques au constructeur parent (AndSpecification)
        parent::__construct(
            // Règle 1: Le statut doit être ACTIVE
            new CostItemHasStatusSpecification(CostItemStatus::ACTIVE),

            // Règle 2: La période de couverture doit être active
            new class() extends AbstractSpecification {
                public function isSatisfiedBy($candidate): bool {
                    /** @var CostItem $candidate */
                    return (new CoveragePeriodIsActiveSpecification())->isSatisfiedBy($candidate->coveragePeriod());
                }
            }
        );
    }
}
