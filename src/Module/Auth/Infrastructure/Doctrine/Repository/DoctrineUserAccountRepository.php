<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Doctrine\Repository;

use App\Core\Domain\Specification\IsUniqueSpecification;
use App\Core\Domain\Specification\SpecificationInterface;
use App\Core\Infrastructure\Persistence\ORMAbstractRepository;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\UserAccount;
use App\Module\Auth\Domain\ValueObject\Password;
use App\Module\SharedContext\Domain\ValueObject\EmailAddress;
use App\Module\SharedContext\Domain\ValueObject\PhoneNumber;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<UserAccount>
 *
 * Implémentation concrète du UserRepositoryInterface pour le module Auth,
 * utilisant Doctrine ORM pour interagir avec la base de données.
 */
class DoctrineUserAccountRepository extends ORMAbstractRepository implements UserRepositoryInterface, PasswordUpgraderInterface
{
    private const ENTITY_CLASS = UserAccount::class;
    private const ALIAS = 'user_account';

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

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof UserAccount) {
            throw new UnsupportedUserException(\sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->changePassword(password: new Password($newHashedPassword));

        $this->add($user);
        $this->getEntityManager()->flush();
    }

    public function add(UserAccount $user): void
    {
        $this->getEntityManager()->persist($user);
    }

    public function delete(UserAccount $user, bool $flush = false): void
    {
        $this->remove($user);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserAccount $user): void
    {
        $this->getEntityManager()->remove($user);
    }

    public function ofId(UserId $id): ?UserAccount
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function ofEmail(EmailAddress $email): ?UserAccount
    {
        $qb = $this->withEmail($email)
            ->query();

        return $qb->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
    }

    private function withEmail(EmailAddress $email): ?self
    {
        return $this->filter(static function (QueryBuilder $qb) use ($email): void {
            $qb->where(\sprintf('%s.email = :email', self::ALIAS))->setParameter('email', $email);
        });
    }

    /**
     * Vérifie si un compte utilisateur avec l'adresse email spécifiée existe déjà,
     * excluant un ID d'utilisateur si fourni. Cela assure que chaque email est unique
     * dans le contexte d'authentification.
     */
    public function existsUserWithEmail(EmailAddress $email, ?UserId $excludeId = null): bool
    {
        $qb = $this->withEmail($email)
            ->query()
            ->select('COUNT('.self::ALIAS.'.id)')
        ;

        if (null !== $excludeId) {
            $qb->andWhere('ua.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function ofPhone(PhoneNumber $phone): ?UserAccount
    {
        $qb = $this->withPhone($phone)
            ->query();

        return $qb->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
    }

    private function withPhone(PhoneNumber $phone): ?self
    {
        return $this->filter(static function (QueryBuilder $qb) use ($phone): void {
            $qb->where(\sprintf('%s.phone = :phone', self::ALIAS))->setParameter('phone', $phone);
        });
    }

    /**
     * Vérifie si un compte utilisateur avec l'adresse phone spécifiée existe déjà,
     * excluant un ID d'utilisateur si fourni. Cela assure que chaque phone est unique
     * dans le contexte d'authentification.
     */
    public function existsUserWithPhone(PhoneNumber $phone, ?UserId $excludeId = null): bool
    {
        $qb = $this->withPhone($phone)
            ->query()
            ->select('COUNT('.self::ALIAS.'.id)')
        ;

        if (null !== $excludeId) {
            $qb->andWhere('ua.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }
}
