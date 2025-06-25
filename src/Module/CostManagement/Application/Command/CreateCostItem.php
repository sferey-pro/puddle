<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Command;

use App\Core\Application\Command\CommandInterface;
use App\Module\CostManagement\Application\DTO\CreateCostItemDTO;

/**
 * Commande pour créer un nouveau poste de coût.
 * Elle encapsule le DTO contenant les données nécessaires.
 */
final readonly class CreateCostItem implements CommandInterface
{
    public function __construct(
        public CreateCostItemDTO $dto,
    ) {
    }
}
