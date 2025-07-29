<?php

declare(strict_types=1);

namespace Account\Registration\Infrastructure\Persistence\Doctrine\Repository;

use Account\Registration\Domain\Repository\RegistrationProcessRepositoryInterface;
use Account\Registration\Domain\Saga\Process\RegistrationSagaProcess;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Kernel\Domain\Saga\SagaStateId;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Implémentation Doctrine du repository RegistrationProcess.
 *
 * ARCHITECTURE :
 * - SINGLE_TABLE inheritance : table `saga_process`
 * - Discriminator : 'registration' pour filtrer nos Sagas
 * - Context JSON contient : userId, identifier_value, identifier_class, channel
 */
final class DoctrineRegistrationProcessRepository extends ServiceEntityRepository
    implements RegistrationProcessRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RegistrationSagaProcess::class);
    }

    // ==================== CRUD BASIQUE ====================

    public function save(RegistrationSagaProcess $process): void
    {
        $this->getEntityManager()->persist($process);
        $this->getEntityManager()->flush();
    }

    public function remove(RegistrationSagaProcess $process): void
    {
        $this->getEntityManager()->remove($process);
        $this->getEntityManager()->flush();
    }

    // ==================== RECHERCHES ESSENTIELLES ====================

    public function findById(SagaStateId $sagaStateId): ?RegistrationSagaProcess
    {
        return $this->find($sagaStateId);
    }

    public function findByUserId(UserId $userId): ?RegistrationSagaProcess
    {
        // Le userId est stocké dans le context JSON
        return $this->createQueryBuilder('p')
            ->where("JSON_UNQUOTE(JSON_EXTRACT(p.context, '$.userId')) = :userId")
            ->setParameter('userId', (string) $userId)
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findActiveByIdentifier(string $identifierValue): ?RegistrationSagaProcess
    {
        // Recherche dans le context JSON pour l'identifier actif
        return $this->createQueryBuilder('p')
            ->where("JSON_UNQUOTE(JSON_EXTRACT(p.context, '$.identifier_value')) = :identifier")
            ->andWhere('p.currentState NOT IN (:finalStates)')
            ->setParameter('identifier', $identifierValue)
            ->setParameter('finalStates', ['completed', 'failed', 'compensation_failed'])
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // ==================== REQUÊTES MÉTIER ====================

    public function findByState(string $state): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.currentState = :state')
            ->setParameter('state', $state)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findStuckProcesses(\DateInterval $stuckSince): array
    {
        $threshold = (new \DateTimeImmutable())->sub($stuckSince);

        return $this->createQueryBuilder('p')
            ->where('p.currentState NOT IN (:finalStates)')
            ->andWhere('p.updatedAt < :threshold')
            ->setParameter('finalStates', ['completed', 'failed', 'compensation_failed'])
            ->setParameter('threshold', $threshold)
            ->orderBy('p.updatedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countActive(): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.currentState NOT IN (:finalStates)')
            ->setParameter('finalStates', ['completed', 'failed', 'compensation_failed'])
            ->getQuery()
            ->getSingleScalarResult();
    }

    // ==================== MAINTENANCE ====================

    public function cleanupCompleted(\DateTimeInterface $before): int
    {
        return $this->createQueryBuilder('p')
            ->delete()
            ->where('p.currentState = :completed')
            ->andWhere('p.updatedAt < :before')
            ->setParameter('completed', 'completed')
            ->setParameter('before', $before)
            ->getQuery()
            ->execute();
    }
}
