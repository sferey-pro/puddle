<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Command;

use App\Module\CostManagement\Application\DTO\AddContributionDTO;
use App\Shared\Application\Command\CommandInterface;

final readonly class UpdateCostContribution implements CommandInterface
{
    public function __construct(
        public AddContributionDTO $dto,
    ) {
    }
}
