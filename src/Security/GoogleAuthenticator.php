<?php

namespace App\Security;

use App\Config\SocialNetwork;
use App\Entity\User;
use App\Entity\UserSocialNetwork;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class GoogleAuthenticator extends AbstractOAuth2Authenticator
{

    protected SocialNetwork $serviceName = SocialNetwork::GOOGLE;

    protected function getUserFromResourceOwner(ResourceOwnerInterface $resourceOwner): ?User
    {
        if (!($resourceOwner instanceof GoogleUser)) {
            throw new \RuntimeException("Expecting google user");
        }

        if (true !== ($resourceOwner->toArray()['email_verified'] ?? null)) {
            throw new AuthenticationException("Email not verified");
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
