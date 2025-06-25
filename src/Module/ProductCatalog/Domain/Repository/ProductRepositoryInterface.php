<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Domain\Repository;

use App\Core\Domain\Repository\RepositoryInterface;
use App\Module\ProductCatalog\Domain\Product;
use App\Module\SharedContext\Domain\ValueObject\ProductId;

/**
 * @extends RepositoryInterface<Product>
 */
interface ProductRepositoryInterface extends RepositoryInterface
{
    public function save(Product $model, bool $flush = false): void;

    public function add(Product $model): void;

    public function remove(Product $model): void;

    public function ofId(ProductId $id): ?Product;
}
