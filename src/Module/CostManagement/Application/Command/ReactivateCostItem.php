<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Command;

use App\Core\Application\Command\CommandInterface;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;

/**
 * Commande représentant l'intention de réactiver un poste de coût archivé.
 */
final readonly class ReactivateCostItem implements CommandInterface
{
    public function __construct(
        public CostItemId $costItemId,
    ) {
    }
}
