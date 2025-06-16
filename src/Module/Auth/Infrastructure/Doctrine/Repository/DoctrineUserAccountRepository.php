<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Doctrine\Repository;

use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\UserAccount;
use App\Module\Auth\Domain\ValueObject\Password;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\SharedContext\Domain\ValueObject\Username;
use App\Shared\Infrastructure\Doctrine\ORMAbstractRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
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

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof UserAccount) {
            throw new UnsupportedUserException(\sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->changePassword(password: new Password($newHashedPassword));

        $this->save($user, true);
    }

    public function save(UserAccount $user, bool $flush = false): void
    {
        $this->add($user);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function add(UserAccount $user): void
    {
        $this->getEntityManager()->persist($user);
    }

    public function ofId(UserId $id): ?UserAccount
    {
        return $this->findOneBy(['id.value' => $id->value]);
    }

    public function ofEmail(Email $email): ?UserAccount
    {
        $qb = $this->withEmail($email)
            ->query();

        return $qb->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
    }

    public function ofUsername(Username $username): ?UserAccount
    {
        $qb = $this->withUsername($username)
            ->query();

        return $qb->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
    }

    private function withEmail(Email $email): ?self
    {
        return $this->filter(static function (QueryBuilder $qb) use ($email): void {
            $qb->where(\sprintf('%s.email.value = :email', self::ALIAS))->setParameter('email', $email->value);
        });
    }

    private function withUsername(Username $username): ?self
    {
        return $this->filter(static function (QueryBuilder $qb) use ($username): void {
            $qb->where(\sprintf('%s.username.value = :username', self::ALIAS))->setParameter('username', $username->value);
        });
    }

    /**
     * Vérifie si un compte utilisateur avec l'adresse email spécifiée existe déjà,
     * excluant un ID d'utilisateur si fourni. Cela assure que chaque email est unique
     * dans le contexte d'authentification.
     */
    public function existsUserWithEmail(Email $email, ?UserId $excludeId = null): bool
    {
        $qb = $this->withEmail($email)
            ->query()
            ->select('COUNT('.self::ALIAS.'.id.value)')
        ;

        if (null !== $excludeId) {
            $qb->andWhere('ua.id.uuid != :excludeId')
                ->setParameter('excludeId', $excludeId->value);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }
}
