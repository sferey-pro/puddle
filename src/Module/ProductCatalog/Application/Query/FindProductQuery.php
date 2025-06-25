<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Application\Query;

use App\Core\Application\Query\QueryInterface;
use App\Module\SharedContext\Domain\ValueObject\ProductId;

final readonly class FindProductQuery implements QueryInterface
{
    public function __construct(
        public ?ProductId $id = null,
    ) {
    }
}
