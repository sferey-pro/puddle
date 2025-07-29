<?php

declare(strict_types=1);

namespace Account\Core\Infrastructure\Persistence\Doctrine\Repository;

use Account\Core\Domain\Model\Account;
use Account\Core\Domain\Repository\AccountRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Implémentation Doctrine du repository Account.
 *
 * APPROCHE :
 * - Utilisation directe de ServiceEntityRepository
 * - Requêtes optimisées avec COUNT pour les vérifications
 * - Pas de flush automatique pour plus de contrôle
 */
final class DoctrineAccountRepository extends ServiceEntityRepository implements AccountRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    // ==================== CRUD BASIQUE ====================

    public function save(Account $account): void
    {
        $this->getEntityManager()->persist($account);
        $this->getEntityManager()->flush();
    }

    public function remove(Account $account): void
    {
        $this->getEntityManager()->remove($account);
        $this->getEntityManager()->flush();
    }

    // ==================== RECHERCHES ESSENTIELLES ====================

    public function findById(UserId $userId): ?Account
    {
        return $this->find($userId);
    }

    public function findByEmail(string $email): ?Account
    {
        return $this->createQueryBuilder('a')
            ->where('a.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByPhone(string $phone): ?Account
    {
        return $this->createQueryBuilder('a')
            ->where('a.phone = :phone')
            ->setParameter('phone', $phone)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // ==================== REQUÊTES OPTIMISÉES ====================

    public function exists(UserId $userId): bool
    {
        $count = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    public function emailExists(string $email): bool
    {
        $count = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    public function phoneExists(string $phone): bool
    {
        $count = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.phone = :phone')
            ->setParameter('phone', $phone)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    // ==================== REQUÊTES MÉTIER ====================

    public function countActive(): int
    {
        return (int) $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.state = :state')
            ->setParameter('state', 'active')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findCreatedBetween(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
