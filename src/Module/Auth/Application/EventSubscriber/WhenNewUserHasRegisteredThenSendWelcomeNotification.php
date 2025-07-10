<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\EventSubscriber;

use App\Module\Auth\Application\Notifier\WelcomeNotification;
use App\Module\Auth\Domain\Event\NewUserHasRegistered;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

/**
 * Réagit à l'événement de domaine `NewUserHasRegistered` pour envoyer la notification de bienvenue.
 *
 * Rôle métier :
 * En tant qu'écouteur d'événement, son unique rôle est de gérer la communication avec le nouvel
 * utilisateur après son inscription. Il est responsable de l'envoi d'une notification (e-mail, SMS, etc.)
 * contenant un lien de connexion pour sa première visite.
 */
#[AsMessageHandler()]
final readonly class WhenNewUserHasRegisteredThenSendWelcomeNotification
{
    public function __construct(
        private NotifierInterface $notifier,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(NewUserHasRegistered $event): void
    {
        $notification = new WelcomeNotification(
            $event->loginLinkDetails(),
            '🎉 Bienvenue sur Puddle !'
        );

        $recipient = new Recipient((string) $event->email(), (string) $event->phoneNumber());
        try {
            $this->notifier->send($notification, $recipient);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Failed to send welcome email for new user.', [
                'recipient' => $event->email(),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
