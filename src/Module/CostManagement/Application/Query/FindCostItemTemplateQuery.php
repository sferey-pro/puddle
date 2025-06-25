<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Query;

use App\Core\Application\Query\QueryInterface;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;

final readonly class FindCostItemTemplateQuery implements QueryInterface
{
    public function __construct(
        public CostItemId $id,
    ) {
    }
}
