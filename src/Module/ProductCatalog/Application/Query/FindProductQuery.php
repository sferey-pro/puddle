<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Application\Query;

use App\Module\SharedContext\Domain\ValueObject\ProductId;
use App\Shared\Application\Query\QueryInterface;

final readonly class FindProductQuery implements QueryInterface
{
    public function __construct(
        public ?ProductId $identifier = null,
    ) {
    }
}
