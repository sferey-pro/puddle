<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Command;

use App\Module\CostManagement\Application\DTO\CreateRecurringCostDTO;
use App\Shared\Application\Command\CommandInterface;

final readonly class CreateRecurringCost implements CommandInterface
{
    public function __construct(
        public CreateRecurringCostDTO $dto,
    ) {
    }
}
