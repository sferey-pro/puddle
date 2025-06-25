<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Query;

use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsQueryHandler;
use App\Module\CostManagement\Application\ReadModel\CostItemTemplateView;
use App\Module\CostManagement\Application\ReadModel\Repository\CostItemTemplateViewRepositoryInterface;

/**
 * Handler for the FindCostItemTemplateQuery.
 * Retrieves a CostItemInstanceView based on its ID.
 */
#[AsQueryHandler]
final readonly class FindCostItemTemplateQueryHandler
{
    public function __construct(
        private CostItemTemplateViewRepositoryInterface $repository,
    ) {
    }

    public function __invoke(FindCostItemTemplateQuery $query): ?CostItemTemplateView
    {
        return $this->repository->findById($query->id);
    }
}
