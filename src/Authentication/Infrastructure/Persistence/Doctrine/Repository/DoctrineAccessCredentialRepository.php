<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Persistence\Doctrine\Repository;

use Authentication\Domain\Model\AccessCredential;
use Authentication\Domain\Repository\AccessCredentialRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineAccessCredentialRepository extends ServiceEntityRepository
    implements AccessCredentialRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccessCredential::class);
    }

    // ========== CRUD ==========

    public function save(AccessCredential $credential): void
    {
        $this->_em->persist($credential);
        $this->_em->flush();
    }

    public function remove(AccessCredential $credential): void
    {
        $this->_em->remove($credential);
        $this->_em->flush();
    }
}
