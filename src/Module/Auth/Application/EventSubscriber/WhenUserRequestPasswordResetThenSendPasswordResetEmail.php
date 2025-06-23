<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\EventSubscriber;

use App\Module\Auth\Domain\Event\PasswordResetRequested;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Réagit à l'événement de domaine `PasswordResetRequested` pour envoyer l'e-mail de réinitialisation.
 *
 * En tant qu'écouteur d'événement, son unique rôle est de gérer la communication avec l'utilisateur
 * lorsqu'une demande de réinitialisation valide a été créée. Cela découple la logique
 * d'envoi de notification de la logique de création de la demande elle-même.
 */
#[AsMessageHandler()]
final class WhenUserRequestPasswordResetThenSendPasswordResetEmail
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function __invoke(PasswordResetRequested $event): void
    {
        $plainToken = $event->plainToken();
        if (null === $plainToken) {
            // Cas de sécurité : si l'événement ne contient pas de token, c'est qu'il ne faut rien envoyer.
            return;
        }

        // Construit l'URL unique que l'utilisateur devra visiter pour réinitialiser son mot de passe.
        $resetUrl = $this->urlGenerator->generate(
            'forgot_password_reset_password',
            ['token' => $plainToken],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@puddle.com', 'Puddle Security'))
            ->to($event->email()->value)
            ->subject('Your password reset request')
            ->htmlTemplate('@Auth/emails/password_reset.html.twig')
            ->context([
                'resetUrl' => $resetUrl,
                'expiresAt' => $event->expiresAt(),
            ]);

        $this->mailer->send($email);
    }
}
