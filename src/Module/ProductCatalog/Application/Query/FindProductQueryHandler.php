<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Application\Query;

use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsQueryHandler;
use App\Module\ProductCatalog\Domain\Exception\ProductException;
use App\Module\ProductCatalog\Domain\Product;
use App\Module\ProductCatalog\Domain\Repository\ProductRepositoryInterface;

#[AsQueryHandler]
final readonly class FindProductQueryHandler
{
    public function __construct(
        private ProductRepositoryInterface $repository,
    ) {
    }

    public function __invoke(FindProductQuery $query): Product
    {
        $product = null;

        if (null !== $query->id) {
            $product = $this->repository->ofId($query->id);
        }

        if (null === $product) {
            throw ProductException::notFoundWithId($query->id);
        }

        return $product;
    }
}
