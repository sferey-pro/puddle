<?php
namespace App\Module\CostManagement\Application\Query;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Shared\Application\Query\QueryInterface;

final readonly class FindCostItemTemplateQuery implements QueryInterface
{
    public function __construct(
        public CostItemId $id
    ) {

    }
}
