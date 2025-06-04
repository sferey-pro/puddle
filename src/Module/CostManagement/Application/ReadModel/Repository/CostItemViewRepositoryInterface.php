<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\ReadModel\Repository;

use App\Module\CostManagement\Application\Query\ListCostItemsQuery;
use App\Module\CostManagement\Application\ReadModel\CostItemView;
use App\Shared\Domain\Repository\PaginatorInterface;

interface CostItemViewRepositoryInterface 
{
    public function findById(string $costItemId): ?CostItemView;
}
