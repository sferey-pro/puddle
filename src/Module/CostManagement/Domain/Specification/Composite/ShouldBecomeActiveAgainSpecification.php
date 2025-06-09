<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification\Composite;

use App\Core\Specification\AbstractSpecification;
use App\Core\Specification\AndSpecification;
use App\Core\Specification\NotSpecification;
use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\Enum\CostItemStatus;
use App\Module\CostManagement\Domain\Specification\CostItemAmountIsSufficientSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemHasStatusSpecification;
use App\Module\CostManagement\Domain\Specification\CoveragePeriodIsActiveSpecification;

/**
 * Spécification qui détermine si un CostItem doit redevenir ACTIF après avoir été FULLY_COVERED.
 * * @template-extends AndSpecification<CostItem>
 */
final class ShouldBecomeActiveAgainSpecification extends AndSpecification
{
    public function __construct()
    {
        parent::__construct(
            // Règle 1: Le statut précédent doit être FULLY_COVERED
            new CostItemHasStatusSpecification(CostItemStatus::FULLY_COVERED),

            // Règle 2: ET le montant ne doit plus être suffisant
            new NotSpecification(new CostItemAmountIsSufficientSpecification()),

            // Règle 3: ET la période de couverture doit toujours être active
            new class() extends AbstractSpecification {
                public function isSatisfiedBy($candidate): bool {
                    /** @var CostItem $candidate */
                    return (new CoveragePeriodIsActiveSpecification())->isSatisfiedBy($candidate->coveragePeriod());
                }
            }
        );
    }
}
