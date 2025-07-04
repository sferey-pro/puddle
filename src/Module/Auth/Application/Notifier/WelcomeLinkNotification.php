<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Notifier;

use App\Module\Auth\Domain\ValueObject\LoginLinkDetails;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Notifier\Recipient\SmsRecipientInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;

/**
 * Représente la notification de bienvenue envoyée à un nouvel utilisateur.
 *
 * Il a la particularité de pouvoir s'adapter au canal de communication disponible
 * (e-mail ou SMS), garantissant que l'utilisateur reçoive bien son accès.
 *
 * Il est utilisé lors de la première connexion d'un utilisateur après son inscription.
 */
class WelcomeLinkNotification extends LoginLinkNotification
{
    public function __construct(
        private LoginLinkDetails $loginLinkDetails,
        private string $subject,
    ) {
    }

    /**
     * Détermine les canaux de communication disponibles pour ce destinataire.
     */
    public function getChannels(object $recipient): array
    {
        if ($recipient instanceof SmsRecipientInterface && $recipient->getPhone()) {
            return ['sms'];
        }

        if ($recipient instanceof EmailRecipientInterface && $recipient->getEmail()) {
            return ['email'];
        }

        return [];
    }

    /**
     * Construit le message de bienvenue au format e-mail.
     */
    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): ?EmailMessage
    {
        $email = NotificationEmail::asPublicEmail()
            ->to($recipient->getEmail())
            ->subject($this->subject)
            ->htmlTemplate('@Auth/emails/welcome_first_login.html.twig')
            ->action('Accéder à mon compte', $this->loginLinkDetails->url())
        ;

        return new EmailMessage($email);
    }

    /**
     * Construit le message de bienvenue au format SMS.
     */
    public function asSmsMessage(SmsRecipientInterface $recipient, ?string $transport = null): ?SmsMessage
    {
        $url = 'puddle.app/login/'.$this->loginLinkDetails->url();
        $message = \sprintf(
            'Bienvenue sur Puddle ! Cliquez ici pour votre première connexion : %s',
            $url
        );

        return new SmsMessage($recipient->getPhone(), $message);
    }
}
