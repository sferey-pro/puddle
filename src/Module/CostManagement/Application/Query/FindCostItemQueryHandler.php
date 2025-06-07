<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Query;

use App\Module\CostManagement\Application\ReadModel\CostItemView;
use App\Module\CostManagement\Application\ReadModel\Repository\CostItemViewRepositoryInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsQueryHandler;

/**
 * Handler for the FindCostItemByIdQuery.
 * Retrieves a CostItemView based on its ID.
 */
#[AsQueryHandler]
final readonly class FindCostItemQueryHandler
{
    public function __construct(
        private CostItemViewRepositoryInterface $repository,
    ) {
    }

    public function __invoke(FindCostItemQuery $query): ?CostItemView
    {
        return $this->repository->findById($query->id);
    }
}
