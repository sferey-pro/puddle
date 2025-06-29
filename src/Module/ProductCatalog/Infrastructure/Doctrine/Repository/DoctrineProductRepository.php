<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Infrastructure\Doctrine\Repository;

use App\Core\Infrastructure\Persistence\ORMAbstractRepository;
use App\Module\ProductCatalog\Domain\Product;
use App\Module\ProductCatalog\Domain\Repository\ProductRepositoryInterface;
use App\Module\SharedContext\Domain\ValueObject\ProductId;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineProductRepository extends ORMAbstractRepository implements ProductRepositoryInterface
{
    private const ENTITY_CLASS = Product::class;
    private const ALIAS = 'catalog_products';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS, self::ALIAS);
    }

    public function save(Product $product, bool $flush = false): void
    {
        $this->add($product);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function add(Product $product): void
    {
        $this->getEntityManager()->persist($product);
    }

    public function remove(Product $product): void
    {
        $this->getEntityManager()->remove($product);
    }

    public function ofId(ProductId $id): ?Product
    {
        return $this->findOneBy(['id.value' => $id->value]);
    }
}
