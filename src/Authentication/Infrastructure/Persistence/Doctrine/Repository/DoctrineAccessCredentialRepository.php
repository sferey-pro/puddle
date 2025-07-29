<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Persistence\Doctrine\Repository;

use Authentication\Domain\Model\AccessCredential\AbstractAccessCredential;
use Authentication\Domain\Repository\AccessCredentialRepositoryInterface;
use Authentication\Domain\ValueObject\Token;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Identity\Domain\ValueObject\Identifier;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Implémentation Doctrine du repository AccessCredential.
 *
 * ARCHITECTURE :
 * - SINGLE_TABLE inheritance : une seule table `access_credentials`
 * - Discriminator column `type` : 'magic_link' ou 'otp'
 * - Gestion unifiée des credentials temporaires
 */
final class DoctrineAccessCredentialRepository extends ServiceEntityRepository
    implements AccessCredentialRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AbstractAccessCredential::class);
    }

    // ==================== CRUD BASIQUE ====================

    public function save(AbstractAccessCredential $credential): void
    {
        $this->getEntityManager()->persist($credential);
        $this->getEntityManager()->flush();
    }

    public function remove(AbstractAccessCredential $credential): void
    {
        $this->getEntityManager()->remove($credential);
        $this->getEntityManager()->flush();
    }

    // ==================== RECHERCHES ESSENTIELLES ====================

    public function findByIdentifierAndUserId(Identifier $identifier, UserId $userId): ?AbstractAccessCredential
    {
        return $this->createQueryBuilder('c')
            ->where('c.identifier = :identifier')
            ->andWhere('c.userId = :userId')
            ->setParameter('identifier', $identifier->value())
            ->setParameter('userId', $userId)
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByToken(Token $token): ?AbstractAccessCredential
    {
        return $this->createQueryBuilder('c')
            ->where('c.token = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findActiveByUserId(UserId $userId): array
    {
        $now = new \DateTimeImmutable();

        return $this->createQueryBuilder('c')
            ->where('c.userId = :userId')
            ->andWhere('c.usedAt IS NULL')
            ->andWhere('c.expiresAt > :now')
            ->setParameter('userId', $userId)
            ->setParameter('now', $now)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findLatestByIdentifier(Identifier $identifier): ?AbstractAccessCredential
    {
        return $this->createQueryBuilder('c')
            ->where('c.identifier = :identifier')
            ->setParameter('identifier', $identifier->value())
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countRecentAttempts(Identifier $identifier, \DateInterval $interval): int
    {
        $since = (new \DateTimeImmutable())->sub($interval);

        $qb = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.identifier = :identifier')
            ->andWhere('c.createdAt >= :since')
            ->setParameter('identifier', $identifier->value())
            ->setParameter('since', $since);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    // ==================== MAINTENANCE ====================

    public function removeExpired(): int
    {
        $now = new \DateTimeImmutable();

        // Utilisation de DQL pour suppression en masse
        return $this->createQueryBuilder('c')
            ->delete()
            ->where('c.expiresAt < :now')
            ->andWhere('c.usedAt IS NULL')
            ->setParameter('now', $now)
            ->getQuery()
            ->execute();
    }

    public function invalidateAllForUser(UserId $userId): void
    {
        $now = new \DateTimeImmutable();

        // Marquer tous les credentials non utilisés comme utilisés
        $this->createQueryBuilder('c')
            ->update()
            ->set('c.usedAt', ':now')
            ->where('c.userId = :userId')
            ->andWhere('c.usedAt IS NULL')
            ->setParameter('userId', $userId)
            ->setParameter('now', $now)
            ->getQuery()
            ->execute();
    }
}
