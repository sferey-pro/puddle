<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Query;

use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Shared\Application\Query\QueryInterface;

/**
 * Query to find a specific CostItem by its ID.
 * Expected to return a CostItemInstanceView object or null if not found.
 */
final readonly class FindCostItemInstanceQuery implements QueryInterface
{
    public function __construct(
        public CostItemId $id,
    ) {
    }
}
