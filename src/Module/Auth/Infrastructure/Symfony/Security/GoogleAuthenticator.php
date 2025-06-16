<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Symfony\Security;

use App\Module\Auth\Domain\Enum\SocialNetwork;
use App\Module\Auth\Domain\SocialLink;
use App\Module\Auth\Domain\UserAccount;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class GoogleAuthenticator extends AbstractOAuth2Authenticator
{
    protected SocialNetwork $serviceName = SocialNetwork::GOOGLE;

    protected function getUserFromResourceOwner(ResourceOwnerInterface $resourceOwner): ?UserAccount
    {
        if (!($resourceOwner instanceof GoogleUser)) {
            throw new \RuntimeException('Expecting google user');
        }

        if (true !== ($resourceOwner->toArray()['email_verified'] ?? null)) {
            throw new AuthenticationException('Email not verified');
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
