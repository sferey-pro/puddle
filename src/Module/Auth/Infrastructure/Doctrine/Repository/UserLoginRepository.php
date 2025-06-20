<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Doctrine\Repository;

use App\Module\Auth\Domain\LoginLink;
use App\Module\Auth\Domain\Repository\LoginLinkRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LoginLink>
 */
class UserLoginRepository extends ServiceEntityRepository implements LoginLinkRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoginLink::class);
    }

    public function save(LoginLink $LoginLink, bool $flush = false): void
    {
        $this->add($LoginLink);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function add(LoginLink $LoginLink): void
    {
        $this->getEntityManager()->persist($LoginLink);
    }

    public function remove(LoginLink $LoginLink): void
    {
        $this->getEntityManager()->remove($LoginLink);
    }
}
