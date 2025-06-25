<?php

declare(strict_types=1);

namespace App\Module\Sales\Infrastructure\Doctrine\Repository;

use App\Core\Infrastructure\Persistence\ORMAbstractRepository;
use App\Module\Sales\Domain\Order;
use App\Module\Sales\Domain\Repository\OrderRepositoryInterface;
use App\Module\Sales\Domain\ValueObject\OrderId;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineOrderRepository extends ORMAbstractRepository implements OrderRepositoryInterface
{
    private const ENTITY_CLASS = Order::class;
    private const ALIAS = 'sales_order';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS, self::ALIAS);
    }

    public function save(Order $order): void
    {
        $this->getEntityManager()->persist($order);
        $this->getEntityManager()->flush();
    }

    public function ofId(OrderId $id): ?Order
    {
        return $this->getEntityManager()->find(Order::class, $id);
    }
}
