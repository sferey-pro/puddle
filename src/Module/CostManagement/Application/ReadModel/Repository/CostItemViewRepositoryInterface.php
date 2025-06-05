<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\ReadModel\Repository;

use App\Module\CostManagement\Application\ReadModel\CostItemView;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Shared\Domain\Repository\RepositoryInterface;

interface CostItemViewRepositoryInterface extends RepositoryInterface
{
    public function findById(CostItemId $identifier): ?CostItemView;
}
