<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Infrastructure\ReadModel\Repository;

use App\Module\CostManagement\Application\ReadModel\CostItemInstanceView;
use App\Module\CostManagement\Application\ReadModel\CostItemTemplateView;
use App\Module\CostManagement\Application\ReadModel\Repository\CostItemInstanceViewRepositoryInterface;
use App\Module\CostManagement\Domain\Exception\CostItemException;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Shared\Infrastructure\Doctrine\ODMAbstractRepository;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;

class DoctrineCostItemInstanceViewRepository extends ODMAbstractRepository implements CostItemInstanceViewRepositoryInterface
{
    private const DOCUMENT_CLASS = CostItemInstanceView::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::DOCUMENT_CLASS);
    }

    public function findById(CostItemId $id): ?CostItemInstanceView
    {
        return parent::findOneBy(['id' => $id->value]);
    }

    public function findAll(): array
    {
        return parent::findAll();
    }

    public function add(CostItemInstanceView $costItem): void
    {
        $this->getDocumentManager()->persist($costItem);
    }

    public function save(CostItemInstanceView|CostItemTemplateView $costItem, bool $flush = false): void
    {
        $this->add($costItem);

        if ($flush) {
            $this->getDocumentManager()->flush();
        }
    }

    public function delete(CostItemInstanceView $costItem, bool $flush = false): void
    {
        $this->remove($costItem);

        if ($flush) {
            $this->getDocumentManager()->flush();
        }
    }

    public function remove(CostItemInstanceView $costItem): void
    {
        $this->getDocumentManager()->remove($costItem);
    }

    public function findOrFail(CostItemId $id): CostItemInstanceView
    {
        $costItemView = $this->findById($id);

        if (null === $costItemView) {
            throw CostItemException::notFoundWithId($id);
        }

        return $costItemView;
    }
}
