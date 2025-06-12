<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\ValueObject;

use App\Shared\Domain\ValueObject\AggregateRootId;

/**
 * Représente l'identifiant unique d'un modèle de coût récurrent.
 */
final readonly class RecurringCostId
{
    use AggregateRootId;
}
