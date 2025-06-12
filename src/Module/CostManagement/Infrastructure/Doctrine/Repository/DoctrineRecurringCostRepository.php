<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Infrastructure\Doctrine\Repository;

use App\Module\CostManagement\Domain\Enum\RecurringCostStatus;
use App\Module\CostManagement\Domain\RecurringCost;
use App\Module\CostManagement\Domain\Repository\RecurringCostRepositoryInterface;
use App\Module\CostManagement\Domain\ValueObject\RecurringCostId;
use App\Shared\Infrastructure\Doctrine\ORMAbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ORMAbstractRepository<RecurringCost>
 */
class DoctrineRecurringCostRepository extends ORMAbstractRepository implements RecurringCostRepositoryInterface
{
    private const ENTITY_CLASS = RecurringCost::class;
    private const ALIAS = 'cost_management_recurring_cost';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS, self::ALIAS);
    }

    public function save(RecurringCost $recurringCost): void
    {
        $this->getEntityManager()->persist($recurringCost);
    }

    public function ofId(RecurringCostId $id): ?RecurringCost
    {
        return $this->findOneBy(['id.value' => $id->value]);
    }

    public function findDueForGeneration(\DateTimeInterface $dateTime): array
    {
        // Cette logique sélectionne les coûts récurrents actifs qui n'ont pas encore été générés ce mois-ci.
        $qb = $this->getRepository()->createQueryBuilder('rc');
        $qb->where('rc.status = :status')
           ->andWhere($qb->expr()->orX(
               $qb->expr()->isNull('rc.lastGeneratedAt'),
               $qb->expr()->lt('rc.lastGeneratedAt', ':startOfMonth')
           ))
           ->setParameter('status', RecurringCostStatus::ACTIVE->value)
           ->setParameter('startOfMonth', $dateTime->modify('first day of this month')->setTime(0, 0));

        $recurringCosts = $qb->getQuery()->getResult();

        // Filtrage en PHP avec la règle CRON
        return array_filter($recurringCosts, static function (RecurringCost $rc) use ($dateTime) {
            return $rc->recurrenceRule()->isDue($dateTime);
        });
    }
}
