<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Infrastructure\Doctrine\Repository;

use App\Core\Domain\Specification\IsUniqueSpecification;
use App\Core\Domain\Specification\SpecificationInterface;
use App\Core\Infrastructure\Persistence\ORMAbstractRepository;
use App\Module\SharedContext\Domain\ValueObject\EmailAddress;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Domain\Repository\UserRepositoryInterface;
use App\Module\UserManagement\Domain\User;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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

    public function countBySpecification(SpecificationInterface $specification): int
    {
        if (!$specification instanceof IsUniqueSpecification) {
            throw new \InvalidArgumentException('This repository can only handle IsUniqueSpecification.');
        }

        try {
            $qb = $this->query();
            $qb->select('COUNT('.self::ALIAS.'.id)')
               ->where(\sprintf(self::ALIAS.'.%s = :value', $specification->field()))
               ->setParameter('value', $specification->value());

            // Si un ID est fourni, on l'exclut de la recherche (cas d'une mise à jour).
            if (null !== $specification->excludeId()) {
                $qb->andWhere(self::ALIAS.'.id != :excludeId')
                   ->setParameter('excludeId', $specification->excludeId());
            }

            return (int) $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException|NonUniqueResultException) {
            // En cas d'erreur ou si aucun résultat n'est trouvé, le compte est 0.
            return 0;
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
        return $this->findOneBy(['id' => $id->value]);
    }

    public function ofEmail(EmailAddress $email): ?User
    {
        return $this->withEmail($email)
            ->select(User::class)
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
    }

    public function withEmail(EmailAddress $email): ?self
    {
        return $this->filter(static function (QueryBuilder $qb) use ($email): void {
            $qb->where(\sprintf('%s.email = :email', self::ALIAS))->setParameter('email', $email->value);
        });
    }

    /**
     * Vérifie si un utilisateur avec l'adresse email spécifiée existe déjà.
     * Si un ID d'exclusion est fourni, cet utilisateur est ignoré, ce qui est crucial
     * pour les scénarios de mise à jour où un utilisateur peut changer son email
     * sans que son propre email actuel ne soit considéré comme un doublon.
     */
    public function existsUserWithEmail(EmailAddress $email, ?UserId $excludeId = null): bool
    {
        $qb = $this->withEmail($email)
            ->query()
            ->select('COUNT('.self::ALIAS.'.id)')
        ;

        if (null !== $excludeId) {
            $qb->andWhere('u.id.uuid != :excludeId')
                ->setParameter('excludeId', $excludeId->value);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }
}
