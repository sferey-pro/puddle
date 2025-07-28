<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Security\Authenticator;

use Account\Core\Domain\Model\Account;
use Authentication\Domain\Enum\SocialNetwork;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class GithubAuthenticator extends AbstractOAuth2Authenticator
{
    protected SocialNetwork $serviceName = SocialNetwork::GITHUB;

    protected function getUserFromResourceOwner(ResourceOwnerInterface $resourceOwner): ?Account
    {
        if (!($resourceOwner instanceof GithubResourceOwner)) {
            throw new \RuntimeException('Expecting Github user');
        }

        $SocialLink = $this->entityManager->getRepository(SocialLink::class)->findOneBy([
            'socialNetwork' => $this->serviceName,
            'socialId' => $resourceOwner->getId(),
        ]);

        if (!$SocialLink) {
            return null;
        }

        return $SocialLink->getUser();
    }
}
