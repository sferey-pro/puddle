<?php

declare(strict_types=1);

namespace App\Module\Sales\Domain\ValueObject;

use App\Core\Domain\ValueObject\AggregateRootId;

/**
 * Représente l'identifiant unique d'une ligne de commande (Aggregat OrderLine).
 */
final class OrderLineId extends AggregateRootId
{
}
