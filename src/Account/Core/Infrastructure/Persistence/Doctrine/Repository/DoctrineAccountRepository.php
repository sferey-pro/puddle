<?php

declare(strict_types=1);

namespace Account\Core\Infrastructure\Persistence\Doctrine\Repository;

use Account\Core\Domain\Model\Account;
use Account\Core\Domain\Repository\AccountRepositoryInterface;
use Kernel\Infrastructure\Persistence\Doctrine\Repository\AbstractDoctrineRepository;
use SharedKernel\Domain\ValueObject\Contact\EmailAddress;
use SharedKernel\Domain\ValueObject\Contact\PhoneNumber;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Implémentation Doctrine du repository Account.
 *
 * APPROCHE :
 * - Requêtes optimisées avec COUNT pour les vérifications
 * - Pas de flush automatique pour plus de contrôle
 *
 * @extends AbstractDoctrineRepository<Account, UserId>
 */
final class DoctrineAccountRepository extends AbstractDoctrineRepository
    implements AccountRepositoryInterface
{
    // Recherche par critère unique
    // ============================

    public function ofUserId(UserId $id): ?Account
    {
        return $this->createQueryBuilder('a')
            ->where('a.id = :userId')
            ->setParameter('uerId', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofEmail(EmailAddress $email): ?Account
    {
        return $this->createQueryBuilder('a')
            ->where('a.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofPhone(PhoneNumber $phone): ?Account
    {
        return $this->createQueryBuilder('a')
            ->where('a.phone = :phone')
            ->setParameter('phone', $phone)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // Vérification existence
    // ======================

    public function existsUserId(UserId $id): bool
    {
        $count = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.id = :userId')
            ->setParameter('userId', $id)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    public function emailExists(EmailAddress $email): bool
    {
        $count = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    public function phoneExists(PhoneNumber $phone): bool
    {
        $count = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.phone = :phone')
            ->setParameter('phone', $phone)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    // Spécifique métier
    // =================

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
