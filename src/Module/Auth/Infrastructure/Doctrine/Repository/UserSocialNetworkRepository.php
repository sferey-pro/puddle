<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Doctrine\Repository;

use App\Module\Auth\Domain\Model\UserSocialNetwork;
use App\Module\Auth\Domain\Repository\UserSocialNetworkRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserSocialNetwork>
 */
class UserSocialNetworkRepository extends ServiceEntityRepository implements UserSocialNetworkRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSocialNetwork::class);
    }

    public function save(UserSocialNetwork $userSocialNetwork, bool $flush = false): void
    {
        $this->add($userSocialNetwork);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function add(UserSocialNetwork $userSocialNetwork): void
    {
        $this->getEntityManager()->persist($userSocialNetwork);
    }

    public function remove(UserSocialNetwork $userSocialNetwork): void
    {
        $this->getEntityManager()->remove($userSocialNetwork);
    }
}
