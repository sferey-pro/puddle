<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Doctrine\Repository;

use App\Module\Auth\Domain\Repository\SocialLinkRepositoryInterface;
use App\Module\Auth\Domain\SocialLink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SocialLink>
 */
class UserSocialNetworkRepository extends ServiceEntityRepository implements SocialLinkRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SocialLink::class);
    }

    public function save(SocialLink $SocialLink, bool $flush = false): void
    {
        $this->add($SocialLink);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function add(SocialLink $SocialLink): void
    {
        $this->getEntityManager()->persist($SocialLink);
    }

    public function remove(SocialLink $SocialLink): void
    {
        $this->getEntityManager()->remove($SocialLink);
    }
}
