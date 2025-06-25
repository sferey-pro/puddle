<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Command;

use App\Core\Application\Command\CommandInterface;
use App\Module\CostManagement\Application\DTO\CreateRecurringCostDTO;

final readonly class CreateRecurringCost implements CommandInterface
{
    public function __construct(
        public CreateRecurringCostDTO $dto,
    ) {
    }
}
