<?php

declare(strict_types=1);

namespace Identity\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Identity\Domain\Model\UserIdentity;
use Identity\Domain\Repository\UserIdentityRepositoryInterface;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Implémentation Doctrine du repository UserIdentity.
 *
 * APPROCHE RELATIONNELLE :
 * - UserIdentity (1) → AttachedIdentifier (N)
 * - Requêtes DBAL directes pour les cas de performance critique
 */
final class DoctrineUserIdentityRepository extends ServiceEntityRepository
    implements UserIdentityRepositoryInterface
{
    private readonly Connection $connection;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserIdentity::class);
        $this->connection = $this->getEntityManager()->getConnection();
    }

    // ==================== CRUD BASIQUE ====================

    public function save(UserIdentity $userIdentity): void
    {
        $this->getEntityManager()->persist($userIdentity);
        $this->getEntityManager()->flush();
    }

    public function remove(UserIdentity $userIdentity): void
    {
        $this->getEntityManager()->remove($userIdentity);
        $this->getEntityManager()->flush();
    }

    // ==================== RECHERCHES ESSENTIELLES ====================

    public function findByUserId(UserId $userId): ?UserIdentity
    {
        return $this->find($userId);
    }

    public function findByIdentifierValue(string $value): ?UserIdentity
    {
        return $this->createQueryBuilder('ui')
            ->innerJoin('ui.identifiers', 'ai')
            ->where('ai.value = :value')
            ->setParameter('value', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByTypedIdentifier(string $type, string $value): ?UserIdentity
    {
        return $this->createQueryBuilder('ui')
            ->innerJoin('ui.identifiers', 'ai')
            ->where('ai.type = :type')
            ->andWhere('ai.value = :value')
            ->setParameter('type', $type)
            ->setParameter('value', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // ==================== REQUÊTES OPTIMISÉES ====================

    public function findUserIdByIdentifier(string $value): ?UserId
    {
        // Requête DBAL directe pour éviter l'hydratation d'objets
        $sql = <<<'SQL'
            SELECT DISTINCT ai.user_id
            FROM identity_attached_identifiers ai
            WHERE ai.identifier_value = :value
            AND ai.is_verified = 1
            LIMIT 1
        SQL;

        $result = $this->connection->fetchOne($sql, ['value' => $value]);

        return $result ? UserId::fromString($result) : null;
    }

    public function existsByTypedIdentifier(string $type, string $value): bool
    {
        // Utilisation de COUNT avec LIMIT pour performance maximale
        $count = $this->createQueryBuilder('ui')
            ->select('COUNT(ui.userId)')
            ->innerJoin('ui.identifiers', 'ai')
            ->where('ai.type = :type')
            ->andWhere('ai.value = :value')
            ->setParameter('type', $type)
            ->setParameter('value', $value)
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }
}
