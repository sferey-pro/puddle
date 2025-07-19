<?php

namespace Account\Core\Infrastructure\Persistence\Doctrine\Repository;

use Account\Core\Domain\Model\Account;
use Account\Core\Domain\Repository\AccountRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineAccountRepository extends ServiceEntityRepository
    implements AccountRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    // ========== CRUD ==========

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
}
