<?php

declare(strict_types=1);

namespace App\Module\User\Infrastructure\Doctrine\Repository;

use App\Module\Shared\Domain\ValueObject\UserId;
use App\Module\User\Domain\Model\User;
use App\Module\User\Domain\Repository\UserRepositoryInterface;
use App\Shared\Infrastructure\Doctrine\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends AbstractRepository implements UserRepositoryInterface
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

    public function ofNativeEmail(array $fieldName): array
    {
        return $this->findBy(['email.value' => $fieldName['email']]);
    }
}
