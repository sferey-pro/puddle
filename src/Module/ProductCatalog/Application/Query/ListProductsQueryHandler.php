<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Application\Query;

use App\Module\ProductCatalog\Domain\Repository\ProductRepositoryInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsQueryHandler;

#[AsQueryHandler]
final readonly class ListProductsQueryHandler
{
    public function __construct(
        private ProductRepositoryInterface $repository,
    ) {
    }

    public function __invoke(ListProductsQuery $query): ProductRepositoryInterface
    {
        $repository = $this->repository;

        if (null !== $query->page && null !== $query->itemsPerPage) {
            $repository = $repository->withPagination($query->page, $query->itemsPerPage);
        } else {
            $repository = $repository->withoutPagination();
        }

        return $repository;
    }
}
