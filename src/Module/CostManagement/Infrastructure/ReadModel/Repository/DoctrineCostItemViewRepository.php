<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Infrastructure\ReadModel\Repository;

use App\Module\CostManagement\Application\ReadModel\CostItemView;
use App\Module\CostManagement\Application\ReadModel\Repository\CostItemViewRepositoryInterface;
use App\Module\CostManagement\Domain\Exception\CostItemException;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Shared\Infrastructure\Doctrine\ODMAbstractRepository;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;

class DoctrineCostItemViewRepository extends ODMAbstractRepository implements CostItemViewRepositoryInterface
{
    private const DOCUMENT_CLASS = CostItemView::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::DOCUMENT_CLASS);
    }

    public function findById(CostItemId $id): ?CostItemView
    {
        return parent::findOneBy(['id' => $id->value]);
    }

    public function findAll(): array
    {
        return parent::findAll();
    }

    public function add(CostItemView $costItem): void
    {
        $this->getDocumentManager()->persist($costItem);
    }

    public function save(CostItemView $costItem, bool $flush = false): void
    {
        $this->add($costItem);

        if ($flush) {
            $this->getDocumentManager()->flush();
        }
    }

    public function delete(CostItemView $costItem, bool $flush = false): void
    {
        $this->remove($costItem);

        if ($flush) {
            $this->getDocumentManager()->flush();
        }
    }

    public function remove(CostItemView $costItem): void
    {
        $this->getDocumentManager()->remove($costItem);
    }

    public function findOrFail(CostItemId $id): CostItemView
    {
        $costItemView = $this->findById($id);

        if (null === $costItemView) {
            throw CostItemException::notFoundWithId($id);
        }

        return $costItemView;
    }
}
