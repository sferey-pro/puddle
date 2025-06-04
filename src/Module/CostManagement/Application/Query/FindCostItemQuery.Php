<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Query;

use App\Shared\Application\Query\QueryInterface;

/**
 * Query to find a specific CostItem by its ID.
 * Expected to return a CostItemView object or null if not found.
 */
final readonly class FindCostItemQuery implements QueryInterface
{
    public function __construct(
        public string $costItemId
    ) {
    }
}
