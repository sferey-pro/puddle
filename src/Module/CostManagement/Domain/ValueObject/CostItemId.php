<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\ValueObject;

use App\Core\Domain\ValueObject\AggregateRootId;

/**
 * Représente l'identifiant unique d'un poste de coût (Aggregat CostItem).
 */
final class CostItemId extends AggregateRootId
{
}
