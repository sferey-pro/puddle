<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\ValueObject;

use App\Core\Domain\ValueObject\AggregateRootId;

/**
 * Représente l'identifiant unique d'un modèle de coût récurrent (Aggregat RecurringCost).
 */
final class RecurringCostId extends AggregateRootId
{
}
