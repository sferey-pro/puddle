<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Symfony\Service\OAuth;

use App\Module\Auth\Domain\Enum\SocialNetwork;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\SocialLink;
use App\Module\Auth\Domain\UserAccount;
use App\Module\Auth\Domain\ValueObject\Password;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

final readonly class OAuthRegistration
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function persist(SocialNetwork $serviceName, ResourceOwnerInterface $resourceOwner): UserAccount
    {
        $user = UserAccount::create(
            id: UserId::generate(),
            password: new Password(md5(random_bytes(10))),
            email: $resourceOwner->getEmail(),
        );

        $user->verified();

        $SocialLink = new SocialLink(
            socialId: $resourceOwner->getId(),
            user: $user,
            socialNetwork: $serviceName,
            isActive: null
        );

        $this->userRepository->add($user);

        return $user;
    }
}
