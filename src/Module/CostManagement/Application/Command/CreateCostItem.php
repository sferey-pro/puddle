<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Command;

use App\Module\CostManagement\Application\DTO\CreateCostItemDTO;
use App\Shared\Application\Command\CommandInterface;

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
