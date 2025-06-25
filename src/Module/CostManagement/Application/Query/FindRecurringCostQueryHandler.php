<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Query;

use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsQueryHandler;
use App\Module\CostManagement\Application\ReadModel\RecurringCostView;
use App\Module\CostManagement\Application\ReadModel\Repository\RecurringCostViewRepositoryInterface;

#[AsQueryHandler]
final class FindRecurringCostQueryHandler
{
    public function __construct(
        private readonly RecurringCostViewRepositoryInterface $repository,
    ) {
    }

    public function __invoke(FindRecurringCostQuery $query): ?RecurringCostView
    {
        return $this->repository->findById($query->id);
    }
}
