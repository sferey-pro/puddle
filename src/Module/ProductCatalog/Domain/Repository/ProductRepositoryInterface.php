<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Domain\Repository;

use App\Module\ProductCatalog\Domain\Product;
use App\Module\SharedContext\Domain\ValueObject\ProductId;
use App\Shared\Domain\Repository\RepositoryInterface;

/**
 * @extends RepositoryInterface<Product>
 */
interface ProductRepositoryInterface extends RepositoryInterface
{
    public function save(Product $model, bool $flush = false): void;

    public function add(Product $model): void;

    public function remove(Product $model): void;

    public function ofIdentifier(ProductId $identifier): ?Product;
}
