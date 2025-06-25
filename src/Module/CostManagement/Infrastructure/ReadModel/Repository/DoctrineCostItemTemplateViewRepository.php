<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Infrastructure\ReadModel\Repository;

use App\Core\Infrastructure\Persistence\ODMAbstractRepository;
use App\Module\CostManagement\Application\ReadModel\CostItemTemplateView;
use App\Module\CostManagement\Application\ReadModel\Repository\CostItemTemplateViewRepositoryInterface;
use App\Module\CostManagement\Domain\Exception\CostItemException;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;

class DoctrineCostItemTemplateViewRepository extends ODMAbstractRepository implements CostItemTemplateViewRepositoryInterface
{
    private const DOCUMENT_CLASS = CostItemTemplateView::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::DOCUMENT_CLASS);
    }

    public function findById(CostItemId $id): ?CostItemTemplateView
    {
        return parent::findOneBy(['id' => $id->value]);
    }

    public function findAll(): array
    {
        return parent::findAll();
    }

    public function add(CostItemTemplateView $costItem): void
    {
        $this->getDocumentManager()->persist($costItem);
    }

    public function save(CostItemTemplateView $costItem, bool $flush = false): void
    {
        $this->add($costItem);

        if ($flush) {
            $this->getDocumentManager()->flush();
        }
    }

    public function delete(CostItemTemplateView $costItem, bool $flush = false): void
    {
        $this->remove($costItem);

        if ($flush) {
            $this->getDocumentManager()->flush();
        }
    }

    public function remove(CostItemTemplateView $costItem): void
    {
        $this->getDocumentManager()->remove($costItem);
    }

    public function findOrFail(CostItemId $id): CostItemTemplateView
    {
        $costItemView = $this->findById($id);

        if (null === $costItemView) {
            throw CostItemException::notFoundWithId($id);
        }

        return $costItemView;
    }
}
