<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Command;

use App\Module\CostManagement\Application\DTO\AddContributionDTO;
use App\Shared\Application\Command\CommandInterface;

/**
 * Commande pour ajouter une contribution à un poste de coût.
 */
final readonly class AddCostContribution implements CommandInterface
{
    public function __construct(
        public AddContributionDTO $dto,
    ) {
    }
}
