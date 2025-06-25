<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Command;

use App\Core\Application\Command\CommandInterface;
use App\Module\CostManagement\Application\DTO\UpdateCostItemDTO;

/**
 * Commande pour mettre à jour un poste de coût existant.
 */
final readonly class UpdateCostItem implements CommandInterface
{
    public function __construct(
        public UpdateCostItemDTO $dto,
    ) {
    }
}
