<?php

declare(strict_types=1);

namespace App\Messenger\EventSubscriber;

use App\Entity\UserLogin;
use App\Messenger\Event\LoginLinkCreated;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class WhenUserCreatedLoginLinkThenSaveUserLogin
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(LoginLinkCreated $event): void
    {
        $loginLinkDetails = $event->getLoginLinkDetails();

        $request = Request::create($loginLinkDetails->getUrl());

        $userLogin = new UserLogin();
        $userLogin->setUuid($event->getIdentifier())
            ->setExpiresAt($loginLinkDetails->getExpiresAt())
            ->setUser($event->getUser())
            ->setHash($request->get('hash'))
            ->setIsVerified(false)
            ->setIpAddress($event->getIpAddressClient())
        ;

        $this->em->persist($userLogin);
    }
}
