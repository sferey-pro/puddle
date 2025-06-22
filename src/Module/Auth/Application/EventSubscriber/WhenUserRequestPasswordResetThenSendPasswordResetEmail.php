<?php

namespace App\Module\Auth\Application\EventSubscriber;

use App\Module\Auth\Domain\Event\PasswordResetRequested;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Gère l'envoi de l'email de réinitialisation de mot de passe
 * lorsqu'une demande est effectuée.
 */
#[AsMessageHandler()]
final class WhenUserRequestPasswordResetThenSendPasswordResetEmail
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function __invoke(PasswordResetRequested $event): void
    {
        // Récupérer le token en clair depuis le payload de l'événement
        $plainToken = $event->plainToken;
        if (null === $plainToken) {
            // Logguer une erreur, car le token est indispensable
            return;
        }

        $resetUrl = $this->urlGenerator->generate(
            'forgot_password_reset_password', // Nom de la route que nous créerons dans la couche UI
            ['token' => $plainToken],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@puddle.com', 'Puddle Security'))
            ->to($event->email->value)
            ->subject('Your password reset request')
            ->htmlTemplate('@Auth/emails/password_reset.html.twig') // Template à créer
            ->context([
                'resetUrl' => $resetUrl,
                'expiresAt' => $event->expiresAt,
            ]);

        $this->mailer->send($email);
    }
}
