<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Query;

use App\Module\CostManagement\Domain\ValueObject\RecurringCostId;
use App\Shared\Application\Query\QueryInterface;

final readonly class FindRecurringCostQuery implements QueryInterface
{
    public function __construct(
        public RecurringCostId $id
    ) {
    }
}
