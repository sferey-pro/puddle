<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Symfony\Services\OAuth;

use App\Module\Auth\Domain\Enum\SocialNetwork;
use App\Module\Auth\Domain\Model\User;
use App\Module\Auth\Domain\Model\UserSocialNetwork;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\ValueObject\Locale;
use App\Module\Auth\Domain\ValueObject\Password;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

final readonly class OAuthRegistration
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function persist(SocialNetwork $serviceName, ResourceOwnerInterface $resourceOwner): User
    {
        $user = new User(
            email: $resourceOwner->getEmail(),
            roles: [],
            password: new Password(md5(random_bytes(10))),
            isVerified: true,
            locale: new Locale(null)
        );

        $userSocialNetwork = new UserSocialNetwork(
            socialId: $resourceOwner->getId(),
            user: $user,
            socialNetwork: $serviceName,
            isActive: null
        );

        $this->userRepository->save($user);

        return $user;
    }
}
