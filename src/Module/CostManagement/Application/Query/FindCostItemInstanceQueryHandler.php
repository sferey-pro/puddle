<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Query;

use App\Module\CostManagement\Application\ReadModel\CostItemInstanceView;
use App\Module\CostManagement\Application\ReadModel\Repository\CostItemInstanceViewRepositoryInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsQueryHandler;

/**
 * Handler for the FindCostItemInstanceQuery.
 * Retrieves a CostItemInstanceView based on its ID.
 */
#[AsQueryHandler]
final readonly class FindCostItemInstanceQueryHandler
{
    public function __construct(
        private CostItemInstanceViewRepositoryInterface $repository,
    ) {
    }

    public function __invoke(FindCostItemInstanceQuery $query): ?CostItemInstanceView
    {
        return $this->repository->findById($query->id);
    }
}
