<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Persistence\Doctrine\Repository;

use Authentication\Domain\Model\LoginAttempt;
use Authentication\Domain\Repository\LoginAttemptRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class LoginAttemptRepository extends ServiceEntityRepository
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
}
