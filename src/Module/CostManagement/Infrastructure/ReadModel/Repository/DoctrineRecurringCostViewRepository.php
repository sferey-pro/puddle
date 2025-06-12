<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Infrastructure\ReadModel\Repository;

use App\Module\CostManagement\Application\ReadModel\RecurringCostView;
use App\Module\CostManagement\Application\ReadModel\Repository\RecurringCostViewRepositoryInterface;
use App\Module\CostManagement\Domain\Exception\RecurringCostException;
use App\Module\CostManagement\Domain\ValueObject\RecurringCostId;
use App\Shared\Infrastructure\Doctrine\ODMAbstractRepository;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;

/**
 * Implémentation du repository pour RecurringCostView avec Doctrine MongoDB.
 *
 * @extends ODMAbstractRepository<RecurringCostView>
 */
class DoctrineRecurringCostViewRepository extends ODMAbstractRepository implements RecurringCostViewRepositoryInterface
{
    private const DOCUMENT_CLASS = RecurringCostView::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::DOCUMENT_CLASS);
    }

    public function findById(RecurringCostId $id): ?RecurringCostView
    {
        return parent::findOneBy(['id' => $id->value]);
    }

    public function findAll(): array
    {
        return parent::findAll();
    }

    public function add(RecurringCostView $costItem): void
    {
        $this->dm->persist($costItem);
    }

    public function save(RecurringCostView $recurringCostView, bool $flush = false): void
    {
        $this->add($recurringCostView);

        if ($flush) {
            $this->getDocumentManager()->flush();
        }
    }

    public function findOrFail(RecurringCostId $id): RecurringCostView
    {
        $costItemView = $this->findById($id);

        if (null === $costItemView) {
            throw RecurringCostException::notFoundWithId($id);
        }

        return $costItemView;
    }

    public function findAllOrderedByCreationDate(): array
    {
        return $this->findBy([], ['createdAt' => 'DESC']);
    }
}
