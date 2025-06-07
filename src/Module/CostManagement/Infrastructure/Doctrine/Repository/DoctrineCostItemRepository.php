<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Infrastructure\Doctrine\Repository;

use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\Enum\CostItemStatus;
use App\Module\CostManagement\Domain\Exception\CostItemException;
use App\Module\CostManagement\Domain\Repository\CostItemRepositoryInterface;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Shared\Domain\Service\SystemTime;
use App\Shared\Infrastructure\Doctrine\ORMAbstractRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineCostItemRepository extends ORMAbstractRepository implements CostItemRepositoryInterface
{
    private const ENTITY_CLASS = CostItem::class;
    private const ALIAS = 'cost_management_cost_item';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS, self::ALIAS);
    }

    public function findActiveAndUncovered(): array
    {
        return $this->withStatus(CostItemStatus::ACTIVE)
            ->withUncovered(SystemTime::now())
            ->select(CostItemId::class)
            ->getQuery()
            ->getResults(AbstractQuery::HYDRATE_ARRAY)
        ;
    }

    public function findOrFail(CostItemId $id): CostItem
    {
        $costItem = $this->ofId($id);

        if (null === $costItem) {
            throw CostItemException::notFoundWithId($id);
        }

        return $costItem;
    }

    public function withStatus(CostItemStatus $status): ?self
    {
        return $this->filter(static function (QueryBuilder $qb) use ($status): void {
            $qb->where(\sprintf('%s.status.value = :status', self::ALIAS))->setParameter('status', $status->value);
        });
    }

    public function withUncovered(\DateTimeImmutable $currentDate): ?self
    {
        return $this->filter(static function (QueryBuilder $qb) use ($currentDate): void {
            $qb->andWhere($qb->expr()->lte('ci.coveragePeriod.startDate', ':currentDate'))
                ->andWhere($qb->expr()->gte('ci.coveragePeriod.endDate', ':currentDate'))
                ->andWhere($qb->expr()->lt('ci.currentAmount.amount', 'ci.targetAmount.amount'))
                ->setParameter('currentDate', $currentDate);
        });
    }

    public function save(CostItem $costItem, bool $flush = false): void
    {
        $this->add($costItem);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function add(CostItem $costItem): void
    {
        $this->getEntityManager()->persist($costItem);
    }

    public function remove(CostItem $costItem): void
    {
        $this->getEntityManager()->remove($costItem);
    }

    public function ofId(CostItemId $id): ?CostItem
    {
        return $this->findOneBy(['id.value' => $id->value]);
    }
}
