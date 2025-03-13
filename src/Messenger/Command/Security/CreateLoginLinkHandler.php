<?php

declare(strict_types=1);

namespace App\Messenger\Command\Security;

use App\Messenger\Attribute\AsCommandHandler;
use App\Messenger\Event\LoginLinkCreated;
use App\Notifier\CustomLoginLinkNotification;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

#[AsCommandHandler]
final class CreateLoginLinkHandler
{
    public function __construct(
        private MessageBusInterface $eventBus,
        private NotifierInterface $notifier,
        private LoginLinkHandlerInterface $loginLinkHandler,
        private RequestStack $requestStack,
    ) {
    }

    public function __invoke(CreateLoginLink $command): void
    {
        $user = $command->getUser();
        $request = $this->requestStack->getCurrentRequest();

        $userRequest = clone $request;
        $userRequest->setLocale($user->getLocale() ?? $request->getDefaultLocale());

        $loginLinkDetails = $this->loginLinkHandler->createLoginLink($user);

        // create a notification based on the login link details
        $notification = new CustomLoginLinkNotification(
            $loginLinkDetails,
            'Welcome to Puddle!' // email subject
        );
        // create a recipient for this user
        $recipient = new Recipient($user->getEmail());

        // send the notification to the user
        $this->notifier->send($notification, $recipient);

        $event = new LoginLinkCreated(
            user: $user,
            identifier: $command->getUuid(),
            loginLinkDetails: $loginLinkDetails,
            ipAddressClient: $request->getClientIp()
        );

        $this->eventBus->dispatch(
            (new Envelope($event))
                ->with(new DispatchAfterCurrentBusStamp())
        );
    }
}
