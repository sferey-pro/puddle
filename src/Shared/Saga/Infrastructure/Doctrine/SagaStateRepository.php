<?php

declare(strict_types=1);

namespace App\Shared\Saga\Infrastructure\Doctrine;

use App\Shared\Saga\Domain\Enum\SagaStatus;
use App\Shared\Saga\Domain\Repository\SagaStateRepositoryInterface;
use App\Shared\Saga\Domain\SagaState;
use App\Shared\Saga\Domain\ValueObject\SagaStateId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Implémentation Doctrine du repository pour l'entité SagaState.
 */
final class SagaStateRepository extends ServiceEntityRepository implements SagaStateRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SagaState::class);
    }

    public function ofId(SagaStateId $id): ?SagaState
    {
        return $this->findOneBy(['id.value' => $id->value]);
    }

    public function nextIdentity(): SagaStateId
    {
        return SagaStateId::create();
    }

    public function save(SagaState $sagaState, bool $flush = false): void
    {
        $this->getEntityManager()->persist($sagaState);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByPayload(string $payloadKey, mixed $payloadValue): ?SagaState
    {
        return $this->createQueryBuilder('s')
            ->where('s.payload ->> :key = :value')
            ->setParameter('key', $payloadKey)
            ->setParameter('value', $payloadValue)
            ->andWhere('s.status NOT IN (:statuses)')
            ->setParameter('statuses', [SagaStatus::COMPLETED->value, SagaStatus::FAILED->value])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
