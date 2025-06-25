<?php

declare(strict_types=1);

namespace App\Module\Sales\Domain\ValueObject;

use App\Core\Domain\ValueObject\AggregateRootId;

/**
 * Représente l'identifiant unique d'une commande (Aggregat Order).
 */
final class OrderId extends AggregateRootId
{
}
