<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Symfony\Security;

use App\Module\Auth\Domain\Enum\SocialNetwork;
use App\Module\Auth\Domain\Model\UserSocialNetwork;
use App\Module\Auth\Domain\UserAccount;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class GithubAuthenticator extends AbstractOAuth2Authenticator
{
    protected SocialNetwork $serviceName = SocialNetwork::GITHUB;

    protected function getUserFromResourceOwner(ResourceOwnerInterface $resourceOwner): ?UserAccount
    {
        if (!($resourceOwner instanceof GithubResourceOwner)) {
            throw new \RuntimeException('Expecting Github user');
        }

        $userSocialNetwork = $this->entityManager->getRepository(UserSocialNetwork::class)->findOneBy([
            'socialNetwork' => $this->serviceName,
            'socialId' => $resourceOwner->getId(),
        ]);

        if (!$userSocialNetwork) {
            return null;
        }

        return $userSocialNetwork->getUser();
    }
}
