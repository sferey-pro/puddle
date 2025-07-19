<?php

declare(strict_types=1);

namespace Identity\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Identity\Domain\Model\UserIdentity;
use Identity\Domain\Repository\UserIdentityRepositoryInterface;

final class DoctrineUserIdentityRepository extends ServiceEntityRepository
    implements UserIdentityRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserIdentity::class);
    }

    // ========== CRUD ==========

    public function save(UserIdentity $userIdentity): void
    {
        $this->_em->persist($userIdentity);
        $this->_em->flush();
    }

    public function remove(UserIdentity $userIdentity): void
    {
        $this->_em->remove($userIdentity);
        $this->_em->flush();
    }
}
