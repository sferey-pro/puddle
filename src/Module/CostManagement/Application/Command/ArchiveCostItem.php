<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Command;

use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Shared\Application\Command\CommandInterface;

/**
 * Commande pour archiver un poste de coût.
 */
final readonly class ArchiveCostItem implements CommandInterface
{
    public function __construct(
        public CostItemId $costItemId,
    ) {
    }
}
