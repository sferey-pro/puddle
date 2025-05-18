<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Doctrine\Repository;

use App\Module\Auth\Domain\Model\UserLogin;
use App\Module\Auth\Domain\Repository\UserLoginRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserLogin>
 */
class UserLoginRepository extends ServiceEntityRepository implements UserLoginRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserLogin::class);
    }

    public function save(UserLogin $userLogin, bool $flush = false): void
    {
        $this->add($userLogin);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function add(UserLogin $userLogin): void
    {
        $this->getEntityManager()->persist($userLogin);
    }

    public function remove(UserLogin $userLogin): void
    {
        $this->getEntityManager()->remove($userLogin);
    }
}
