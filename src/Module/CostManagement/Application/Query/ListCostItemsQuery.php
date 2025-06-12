<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Query;

use App\Shared\Application\Query\QueryInterface;

/**
 * Query to list CostItems with pagination, filtering, and sorting.
 * Expected to return a PaginatorInterface of CostItemInstanceView objects.
 */
final readonly class ListCostItemsQuery implements QueryInterface
{
    public const DEFAULT_PAGE = 1;
    public const DEFAULT_ITEMS_PER_PAGE = 10;
    public const DEFAULT_SORT_ORDER = 'asc';

    public function __construct(
        public int $page = self::DEFAULT_PAGE,
        public int $itemsPerPage = self::DEFAULT_ITEMS_PER_PAGE,
        public ?string $status = null, // e.g., 'active', 'covered', 'archived' (from CostItemStatus enum values)
        public ?string $nameContains = null, // To filter by name
        public ?string $sortBy = null, // e.g., 'name', 'targetAmount', 'startDate', 'status'
        public string $sortOrder = self::DEFAULT_SORT_ORDER, // 'asc' or 'desc'
    ) {
        if ($this->page < 1) {
            throw new \InvalidArgumentException('Page number must be 1 or greater.');
        }
        if ($this->itemsPerPage < 1) {
            throw new \InvalidArgumentException('Items per page must be 1 or greater.');
        }
        if (!\in_array($this->sortOrder, ['asc', 'desc'], true)) {
            throw new \InvalidArgumentException('Sort order must be "asc" or "desc".');
        }
    }
}
