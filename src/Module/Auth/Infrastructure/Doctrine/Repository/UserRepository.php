<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Doctrine\Repository;

use App\Module\Auth\Domain\Model\User;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\ValueObject\Password;
use App\Module\Shared\Domain\ValueObject\Email;
use App\Module\Shared\Domain\ValueObject\UserId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface, PasswordUpgraderInterface
{
    private const ENTITY_CLASS = User::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(\sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->changePassword(password: new Password($newHashedPassword));

        $this->save($user, true);
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

    public function ofIdentifier(UserId $identifier): ?User
    {
        return $this->findOneBy(['identifier.value' => $identifier->value]);
    }

    public function ofEmail(Email $email): ?User
    {
        return $this->findOneBy(['email.value' => $email]);
    }

    public function ofNativeEmail(array $fieldName): array
    {
        return $this->findBy(['email.value' => $fieldName['email']]);
    }
}
