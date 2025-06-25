<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Command;

use App\Core\Application\Command\CommandInterface;

/**
 * Commande pour supprimer une contribution d'un poste de coût.
 */
final readonly class RemoveCostContribution implements CommandInterface
{
    public function __construct(
        public string $costItemId,
        public string $contributionId,
    ) {
    }
}
