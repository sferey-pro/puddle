<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Specification;

use App\Core\Specification\AbstractSpecification;
use App\Module\CostManagement\Domain\Enum\CostItemStatus;

/**
 * Spécification qui vérifie si le statut d'un poste de coût est 'ARCHIVED'.
 */
final class CostItemIsArchivedSpecification extends CostItemHasStatusSpecification
{
    public function __construct()
    {
        parent::__construct(CostItemStatus::ARCHIVED);
    }
}
