<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\Security;

use App\Module\Auth\Application\Event\LoginLinkCreated;
use App\Module\Auth\Application\Notifier\CustomLoginLinkNotification;
use App\Shared\Application\Event\EventBusInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

/**
 * @todo
 */
#[AsCommandHandler]
final class CreateLoginLinkHandler
{
    public function __construct(
        private EventBusInterface $eventBus,
        private NotifierInterface $notifier,
        private LoginLinkHandlerInterface $loginLinkHandler,
        private RequestStack $requestStack,
    ) {
    }

    public function __invoke(CreateLoginLink $command): void
    {
        $user = $command->user;
        $request = $this->requestStack->getCurrentRequest();

        $userRequest = clone $request;
        $userRequest->setLocale($user->locale()->value ?? $request->getDefaultLocale());

        $loginLinkDetails = $this->loginLinkHandler->createLoginLink($user);

        // create a notification based on the login link details
        $notification = new CustomLoginLinkNotification(
            $loginLinkDetails,
            'Welcome to Puddle!' // email subject
        );
        // create a recipient for this user
        $recipient = new Recipient($user->email()->value);

        // send the notification to the user
        $this->notifier->send($notification, $recipient);

        $event = new LoginLinkCreated(
            user: $user,
            identifier: $command->identifier,
            loginLinkDetails: $loginLinkDetails,
            ipAddressClient: $request->getClientIp()
        );

        $this->eventBus->publish($event);
    }
}
