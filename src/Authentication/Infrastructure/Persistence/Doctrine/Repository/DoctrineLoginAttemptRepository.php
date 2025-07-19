<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Persistence\Doctrine\Repository;

use Authentication\Domain\Model\LoginAttempt;
use Authentication\Domain\Repository\LoginAttemptRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SharedKernel\Domain\ValueObject\Identity\UserId;

final class DoctrineLoginAttemptRepository extends ServiceEntityRepository
    implements LoginAttemptRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoginAttempt::class);
    }

    // ========== CRUD ==========

    public function save(LoginAttempt $loginAttempt): void
    {
        $this->_em->persist($loginAttempt);
        $this->_em->flush();
    }

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

    public function countRecentAttempts(string $identifier, \DateInterval $period): int
    {
        $since = (new \DateTimeImmutable())->sub($period);

        $count = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.identifier = :identifier')
            ->andWhere('a.attemptedAt > :since')
            ->andWhere('a.successful = :success')
            ->setParameter('identifier', $identifier)
            ->setParameter('since', $since)
            ->setParameter('success', false)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $count;
    }

    public function countRecentAttemptsFromIp(string $ipAddress, \DateInterval $period): int
    {
        $since = (new \DateTimeImmutable())->sub($period);

        $count = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.ipAddress = :ip')
            ->andWhere('a.attemptedAt > :since')
            ->andWhere('a.successful = :success')
            ->setParameter('ip', $ipAddress)
            ->setParameter('since', $since)
            ->setParameter('success', false)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $count;
    }

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
