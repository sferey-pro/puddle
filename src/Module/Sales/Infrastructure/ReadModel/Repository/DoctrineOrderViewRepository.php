<?php

declare(strict_types=1);

namespace App\Module\Sales\Infrastructure\ReadModel\Repository;

use App\Module\Sales\Application\ReadModel\OrderView;
use App\Module\Sales\Application\ReadModel\Repository\OrderViewRepositoryInterface;
use App\Module\Sales\Domain\ValueObject\OrderId;
use App\Shared\Infrastructure\Doctrine\ODMAbstractRepository;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;

final class DoctrineOrderViewRepository extends ODMAbstractRepository implements OrderViewRepositoryInterface
{
    private const DOCUMENT_CLASS = OrderView::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::DOCUMENT_CLASS);
    }

    public function save(OrderView $orderView): void
    {
        $this->dm->persist($orderView);
        $this->dm->flush();
    }

    public function ofId(OrderId $id): ?OrderView
    {
        return parent::findOneBy(['id' => $id->value]);
    }

    public function findAll(): array
    {
        return parent::findAll();
    }
}
