<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Persistence\Doctrine\Repository;

use Authentication\Domain\Model\LoginAttempt;
use Authentication\Domain\Repository\LoginAttemptRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Identity\Domain\ValueObject\Identifier;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Implémentation Doctrine du repository LoginAttempt.
 *
 * OPTIMISATIONS :
 * - Index sur (identifier, attempted_at, successful) pour rate limiting
 * - Index sur (ip_address, attempted_at, successful) pour blocage IP
 * - Requêtes COUNT optimisées pour performance
 */
final class DoctrineLoginAttemptRepository extends ServiceEntityRepository
    implements LoginAttemptRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoginAttempt::class);
    }

    // ==================== CRUD BASIQUE ====================

    public function save(LoginAttempt $attempt): void
    {
        $this->getEntityManager()->persist($attempt);
        $this->getEntityManager()->flush();
    }

    // ==================== RATE LIMITING ====================

    public function countFailedAttemptsByIdentifier(string $identifier, \DateInterval $period): int
    {
        $since = (new \DateTimeImmutable())->sub($period);

        return (int) $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.identifier = :identifier')
            ->andWhere('a.attemptedAt > :since')
            ->andWhere('a.successful = false')
            ->setParameter('identifier', $identifier)
            ->setParameter('since', $since)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countFailedAttemptsByIp(string $ipAddress, \DateInterval $period): int
    {
        $since = (new \DateTimeImmutable())->sub($period);

        return (int) $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.ipAddress = :ip')
            ->andWhere('a.attemptedAt > :since')
            ->andWhere('a.successful = false')
            ->setParameter('ip', $ipAddress)
            ->setParameter('since', $since)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findLastSuccessfulByIdentifier(string $identifier): ?LoginAttempt
    {
        return $this->createQueryBuilder('a')
            ->where('a.identifier = :identifier')
            ->andWhere('a.successful = true')
            ->setParameter('identifier', $identifier)
            ->orderBy('a.attemptedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // ==================== HISTORIQUE & AUDIT ====================

    public function findRecentByUserId(UserId $userId, int $limit = 10): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.userId = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('a.attemptedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findSuspiciousIps(\DateInterval $period, int $threshold = 5): array
    {
        $since = (new \DateTimeImmutable())->sub($period);

        $result = $this->createQueryBuilder('a')
            ->select('a.ipAddress', 'COUNT(a.id) as failureCount')
            ->where('a.attemptedAt > :since')
            ->andWhere('a.successful = false')
            ->setParameter('since', $since)
            ->groupBy('a.ipAddress')
            ->having('COUNT(a.id) >= :threshold')
            ->setParameter('threshold', $threshold)
            ->orderBy('failureCount', 'DESC')
            ->getQuery()
            ->getResult();

        // Transformer en array associatif [ip => count]
        $suspiciousIps = [];
        foreach ($result as $row) {
            $suspiciousIps[$row['ipAddress']] = (int) $row['failureCount'];
        }

        return $suspiciousIps;
    }

    public function findRecentByIdentifier(Identifier $identifier, int $windowMinutes = 30): array {
        $since = (new \DateTimeImmutable())->modify("-{$windowMinutes} minutes");

        return $this->createQueryBuilder('a')
            ->where('a.identifier = :identifier')
            ->andWhere('a.attemptedAt >= :since')
            ->setParameter('identifier', $identifier->value())
            ->setParameter('since', $since)
            ->orderBy('a.attemptedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findRecentByIp(string $ipAddress, int $windowMinutes = 30): array {
        $since = (new \DateTimeImmutable())->modify("-{$windowMinutes} minutes");

        return $this->createQueryBuilder('a')
            ->where('a.ipAddress = :ipAddress')
            ->andWhere('a.attemptedAt >= :since')
            ->setParameter('ipAddress', $ipAddress)
            ->setParameter('since', $since)
            ->orderBy('a.attemptedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // ==================== MAINTENANCE ====================

    public function removeOlderThan(\DateTimeInterface $before): int
    {
        return $this->createQueryBuilder('a')
            ->delete()
            ->where('a.attemptedAt < :before')
            ->setParameter('before', $before)
            ->getQuery()
            ->execute();
    }
}
