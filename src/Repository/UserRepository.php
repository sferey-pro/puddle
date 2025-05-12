<?php

declare(strict_types=1);

namespace App\Repository;

use App\Doctrine\AbstractRepository;
use App\Entity\User;
use App\Entity\ValueObject\UserId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends AbstractRepository implements PasswordUpgraderInterface
{
    private const ENTITY_CLASS = User::class;
    private const ALIAS = 'user';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS, self::ALIAS);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(\sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
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
}
