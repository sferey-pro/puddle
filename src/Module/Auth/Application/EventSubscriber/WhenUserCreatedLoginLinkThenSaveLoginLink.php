<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\EventSubscriber;

use App\Module\Auth\Application\Notifier\CustomLoginLinkNotification;
use App\Module\Auth\Application\Query\FindUserByIdentifierQuery;
use App\Module\Auth\Domain\Event\LoginLinkGenerated;
use App\Shared\Application\Query\QueryBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

#[AsMessageHandler()]
class WhenUserCreatedLoginLinkThenSaveLoginLink
{
    public function __construct(
        private NotifierInterface $notifier,
        public QueryBusInterface $queryBus)
    {
    }

    public function __invoke(LoginLinkGenerated $event): void
    {
        $user = $this->queryBus->ask(new FindUserByIdentifierQuery((string) $event->email()));

        $notification = new CustomLoginLinkNotification(
            $event->loginLinkDetails(),
            'Welcome to Puddle!' // email subject
        );

        $recipient = new Recipient((string) $user->email());
        $this->notifier->send($notification, $recipient);
    }
}
