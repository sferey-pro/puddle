<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Domain\Exception;

use App\Module\ProductCatalog\Domain\ValueObject\ProductId;

final class ProductNotFoundException extends \Exception
{
    public static function withProductId(ProductId $identifier): self
    {
        return new self(
            \sprintf('Product not found with identifier : %s', [$identifier])
        );
    }
}
