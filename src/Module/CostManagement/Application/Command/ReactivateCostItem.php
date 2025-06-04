<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Command;

use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Shared\Application\Command\CommandInterface;

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
