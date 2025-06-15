<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Doctrine\Repository;

use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\UserAccount;
use App\Module\Auth\Domain\ValueObject\Password;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<UserAccount>
 */
class DoctrineUserAccountRepository extends ServiceEntityRepository implements UserRepositoryInterface, PasswordUpgraderInterface
{
    private const ENTITY_CLASS = UserAccount::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS);
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
        return $this->findOneBy(['email.value' => $email->value]);
    }

    public function ofNativeEmail(array $fieldName): array
    {
        return $this->findBy(['email.value' => $fieldName['email']]);
    }
}
