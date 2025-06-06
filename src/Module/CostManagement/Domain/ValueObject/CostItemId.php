<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\ValueObject;

use App\Shared\Domain\ValueObject\AggregateRootId;

/**
 * Représente l'identifiant unique d'un agrégat CostItem.
 * Il s'agit d'un Value Object basé sur un UUID v7.
 */
final readonly class CostItemId implements \Stringable
{
    use AggregateRootId;
}
