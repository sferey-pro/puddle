<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\EventSubscriber;

use App\Module\Auth\Application\Notifier\CustomLoginLinkNotification;
use App\Module\Auth\Domain\Event\LoginLinkGenerated;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

#[AsMessageHandler()]
class WhenLoginLinkGeneratedThenSendLoginNotification
{
    public function __construct(
        private NotifierInterface $notifier,
    ) {
    }

    public function __invoke(LoginLinkGenerated $event): void
    {
        $notification = new CustomLoginLinkNotification(
            $event->loginLinkDetails(),
            'Votre lien de connexion Puddle' // email subject
        );

        $recipient = new Recipient((string) $event->email());
        $this->notifier->send($notification, $recipient);
    }
}
