<?php

declare(strict_types=1);

namespace App\Services\OAuth;

use App\Config\SocialNetwork;
use App\Entity\User;
use App\Entity\UserSocialNetwork;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Uid\Uuid;

final readonly class OAuthRegistration
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function persist(SocialNetwork $serviceName, ResourceOwnerInterface $resourceOwner): User
    {
        $userSocialNetwork = new UserSocialNetwork();
        $userSocialNetwork
            ->setSocialNetwork($serviceName)
            ->setSocialId($resourceOwner->getId())
            ->setAsActive()
        ;

        $user = new User();

        $user->setUuid(Uuid::v7())
            ->setPassword(md5(random_bytes(10)))
            ->setEmail($resourceOwner->getEmail())
            ->setIsVerified(true)
            ->addUserSocialNetwork($userSocialNetwork)
        ;

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function getEmail(ResourceOwnerInterface $resourceOwner)
    {
    }
}
