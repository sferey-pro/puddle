<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Infrastructure\Doctrine\Repository;

use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Domain\Repository\CheckUserByEmailInterface;
use App\Module\UserManagement\Domain\Repository\UserRepositoryInterface;
use App\Module\UserManagement\Domain\User;
use App\Shared\Infrastructure\Doctrine\AbstractRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends AbstractRepository implements UserRepositoryInterface, CheckUserByEmailInterface
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

    public function ofIdentifier(UserId $identifier): ?User
    {
        return $this->findOneBy(['identifier.value' => $identifier->value]);
    }

    public function withEmail(Email $email): ?self
    {
        return $this->filter(static function (QueryBuilder $qb) use ($email): void {
            $qb->where(\sprintf('%s.email.value = :email', self::ALIAS))->setParameter('email', $email->value);
        });
    }

    public function ofNativeEmail(array $fieldName): array
    {
        return $this->findBy(['email.value' => $fieldName['email']]);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function existsEmail(Email $email): ?UserId
    {
        $userId = $this->withEmail($email)
            ->select(UserId::class)
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY)
        ;

        return $userId['identifier'] ?? null;
    }
}
