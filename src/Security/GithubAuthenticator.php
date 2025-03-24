<?php

namespace App\Security;

use App\Config\SocialNetwork;
use App\Entity\User;
use App\Entity\UserSocialNetwork;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class GithubAuthenticator extends AbstractOAuth2Authenticator
{
    protected SocialNetwork $serviceName = SocialNetwork::GITHUB;

    protected function getUserFromResourceOwner(ResourceOwnerInterface $resourceOwner): ?User
    {
        if (!($resourceOwner instanceof GithubResourceOwner)) {
            throw new \RuntimeException("Expecting Github user");
        }

        $userSocialNetwork = $this->entityManager->getRepository(UserSocialNetwork::class)->findOneBy([
            'socialNetwork' => $this->serviceName,
            'socialId' => $resourceOwner->getId()
        ]);

        if(!$userSocialNetwork) {
            return null;
        }

        return $userSocialNetwork->getUser();
    }
}
