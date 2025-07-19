<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Persistence\Doctrine\Repository;

use Authentication\Domain\Model\AccessCredential\AbstractAccessCredential;
use Authentication\Domain\Repository\AccessCredentialRepositoryInterface;
use Authentication\Domain\ValueObject\Token;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineAccessCredentialRepository extends ServiceEntityRepository
    implements AccessCredentialRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AbstractAccessCredential::class);
    }

    // ========== CRUD ==========

    public function save(AbstractAccessCredential $credential): void
    {
        $this->_em->persist($credential);
        $this->_em->flush();
    }

    public function remove(AbstractAccessCredential $credential): void
    {
        $this->_em->remove($credential);
        $this->_em->flush();
    }

    public function findByToken(Token $token): ?AbstractAccessCredential
    {
        return $this->createQueryBuilder('a')
            ->where('a.token = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getResult();
    }

    public function removeExpired(): int
    {
        return $this->createQueryBuilder('c')
            ->delete()
            ->where('c.expiresAt < :now')
            ->andWhere('c.usedAt IS NULL')
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->execute();
    }
}
