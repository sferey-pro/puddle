<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Infrastructure\Doctrine\Repository;

use App\Core\Infrastructure\Persistence\ORMAbstractRepository;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Domain\Profile;
use App\Module\UserManagement\Domain\Repository\ProfileRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ORMAbstractRepository<Profile>
 *
 * @implements ProfileRepositoryInterface
 */
final class DoctrineProfileRepository extends ORMAbstractRepository implements ProfileRepositoryInterface
{
    private const ENTITY_CLASS = Profile::class;
    private const ALIAS = 'profile';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS, self::ALIAS);
    }

    public function save(Profile $userProfile, bool $flush = false): void
    {
        $this->add($userProfile);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function add(Profile $userProfile): void
    {
        $this->getEntityManager()->persist($userProfile);
    }

    public function remove(Profile $userProfile): void
    {
        $this->getEntityManager()->remove($userProfile);
    }

    /**
     * Récupère un Profile par l'identifiant de l'utilisateur associé.
     */
    public function ofId(UserId $userId): ?Profile
    {
        return $this->findOneBy(['userId' => $userId->value]);
    }

    /**
     * Vérifie si un profil avec le nom d'utilisateur spécifié existe déjà.
     * Permet d'exclure le profil d'un utilisateur donné de la vérification,
     * ce qui est nécessaire lors des mises à jour du nom d'utilisateur.
     */
    public function existsProfileWithUsername(string $username, ?UserId $excludeId = null): bool
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.username.value = :username')
            ->setParameter('username', $username);

        if (null !== $excludeId) {
            $qb->andWhere('p.userId.uuid != :excludeId')
                ->setParameter('excludeId', $excludeId->value);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }
}
