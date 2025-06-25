<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Infrastructure\Doctrine\Repository;

use App\Core\Infrastructure\Persistence\ORMAbstractRepository;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Domain\Repository\UserRepositoryInterface;
use App\Module\UserManagement\Domain\User;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * Implémentation concrète du UserRepositoryInterface utilisant Doctrine ORM.
 * Cet "Adapter" connecte le domaine UserManagement à la base de données relationnelle.
 */
class DoctrineUserRepository extends ORMAbstractRepository implements UserRepositoryInterface
{
    private const ENTITY_CLASS = User::class;
    private const ALIAS = 'user';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS, self::ALIAS);
    }

    public function save(User $user, bool $flush = false): void
    {
        $this->add($user);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function add(User $user): void
    {
        $this->getEntityManager()->persist($user);
    }

    public function remove(User $user): void
    {
        $this->getEntityManager()->remove($user);
    }

    public function ofId(UserId $id): ?User
    {
        return $this->findOneBy(['id.value' => $id->value]);
    }

    public function ofEmail(Email $email): ?User
    {
        return $this->withEmail($email)
            ->select(User::class)
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
    }

    public function withEmail(Email $email): ?self
    {
        return $this->filter(static function (QueryBuilder $qb) use ($email): void {
            $qb->where(\sprintf('%s.email.value = :email', self::ALIAS))->setParameter('email', $email->value);
        });
    }

    /**
     * Vérifie si un utilisateur avec l'adresse email spécifiée existe déjà.
     * Si un ID d'exclusion est fourni, cet utilisateur est ignoré, ce qui est crucial
     * pour les scénarios de mise à jour où un utilisateur peut changer son email
     * sans que son propre email actuel ne soit considéré comme un doublon.
     */
    public function existsUserWithEmail(Email $email, ?UserId $excludeId = null): bool
    {
        $qb = $this->withEmail($email)
            ->query()
            ->select('COUNT('.self::ALIAS.'.id.value)')
        ;

        if (null !== $excludeId) {
            $qb->andWhere('u.id.uuid != :excludeId')
                ->setParameter('excludeId', $excludeId->value);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }
}
