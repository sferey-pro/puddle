<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Notifier;

use App\Module\Auth\Domain\AccessCredential\AccessCredentialInterface;
use App\Module\Auth\Domain\AccessCredential\MagicLinkCredential;
use App\Module\Auth\Domain\AccessCredential\OtpCredential;
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
class WelcomeNotification extends LoginLinkNotification implements EmailNotificationInterface, SmsNotificationInterface
{
    public function __construct(
        private AccessCredentialInterface $credential,
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
        if (!$this->credential instanceof MagicLinkCredential) {
            throw new \LogicException('Cannot send welcome email with a non-link credential.');
        }

        $email = NotificationEmail::asPublicEmail()
            ->to($recipient->getEmail())
            ->subject($this->subject)
            ->htmlTemplate('@Auth/emails/welcome_first_login.html.twig')
            ->action('Accéder à mon compte', $this->credential->url)
        ;

        return new EmailMessage($email);
    }

    /**
     * Construit le message de bienvenue au format SMS.
     */
    public function asSmsMessage(SmsRecipientInterface $recipient, ?string $transport = null): ?SmsMessage
    {
        if (!$this->credential instanceof OtpCredential) {
            throw new \LogicException('Cannot send welcome email with a non-link credential.');
        }

        $message = \sprintf(
            'Bienvenue sur Puddle !Votre code de connexion est : %s',
            $this->credential->code
        );

        return new SmsMessage($recipient->getPhone(), $message);
    }
}
