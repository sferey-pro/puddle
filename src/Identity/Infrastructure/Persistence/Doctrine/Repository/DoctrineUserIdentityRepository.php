<?php

declare(strict_types=1);

namespace Identity\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Identity\Domain\Model\UserIdentity;
use Identity\Domain\Repository\UserIdentityRepositoryInterface;
use SharedKernel\Domain\ValueObject\Identity\UserId;

final class DoctrineUserIdentityRepository extends ServiceEntityRepository
    implements UserIdentityRepositoryInterface
{
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly Connection $connection
    ) {
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

    /**
     * Recherche une UserIdentity par son UserId.
     */
    public function findByUserId(UserId $userId): ?UserIdentity
    {
        return $this->createQueryBuilder('ui')
            ->where('ui.id = :id')
            ->setParameter('id', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Recherche par valeur d'identifier, peu importe le type.
     * Utilise l'index (identifier_type, identifier_value) pour performance.
     */
    public function findByIdentifierValue(string $value): ?UserIdentity
    {
        return $this->createQueryBuilder('ui')
            ->innerJoin('ui.identifiers', 'ai')
            ->where('ai.value = :value')
            ->setParameter('value', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Recherche avec type spécifique (plus rapide si on connait le type).
     */
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

    /**
     * Version directe pour performance maximale.
     */
    public function findUserIdByIdentifier(string $value): ?UserId
    {
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
}
