<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Query;

use App\Module\CostManagement\Application\ReadModel\Repository\CostItemViewRepositoryInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsQueryHandler;

/**
 * Handler for the ListCostItemsQuery.
 * Retrieves a paginated list of CostItemView objects based on query parameters.
 */
#[AsQueryHandler]
final readonly class ListCostItemsQueryHandler
{
    public function __construct(
        private CostItemViewRepositoryInterface $repository,
    ) {
    }

    public function __invoke(ListCostItemsQuery $query): CostItemViewRepositoryInterface
    {
        $repository = $this->repository;

        if (null !== $query->page && null !== $query->itemsPerPage) {
            $repository = $repository->withPagination($query->page, $query->itemsPerPage);
        }

        return $repository;
    }
}
