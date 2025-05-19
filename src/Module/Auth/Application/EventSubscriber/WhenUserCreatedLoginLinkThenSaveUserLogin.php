<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\EventSubscriber;

use App\Module\Auth\Application\Event\LoginLinkCreated;
use App\Module\Auth\Domain\Repository\UserLoginRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Http\LoginLink\LoginLinkDetails;

#[AsMessageHandler]
class WhenUserCreatedLoginLinkThenSaveUserLogin
{
    public function __construct(
        private UserLoginRepositoryInterface $userLoginRepository,
    ) {
    }

    public function __invoke(LoginLinkCreated $event): void
    {
        /** @var LoginLinkDetails $loginLinkDetails */
        $loginLinkDetails = $event->loginLinkDetails;

        $request = Request::create($loginLinkDetails->getUrl());

        $userLogin = new UserLogin(
            identifier: $event->identifier,
            expiresAt: $loginLinkDetails->getExpiresAt(),
            user: $event->user,
            hash: $request->get('hash'),
            isVerified: false,
            ipAddress: $event->ipAddressClient
        );

        $this->userLoginRepository->save($userLogin, true);
    }
}
