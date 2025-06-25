<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\EventSubscriber;

use App\Module\Auth\Domain\Event\UserRegistered;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Infrastructure\Symfony\Security\EmailVerifier;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as MimeEmail;

/**
 * @final
 *
 * Rôle : Cet event subscriber est responsable d'envoyer un e-mail de bienvenue
 * et de vérification à l'utilisateur une fois que son compte a été créé avec succès
 * via le processus d'inscription standard.
 *
 * Il écoute l'événement `UserRegistered` qui marque le début de la saga.
 * Dans une version plus poussée, il pourrait écouter `UserRegistrationSagaCompleted`.
 */

#[AsEventListener(event: UserRegistered::class, method: 'onUserRegistered')]
final readonly class WhenUserRegistrationSagaCompletedThenSendWelcomeEmail
{
    public function __construct(
        private EmailVerifier $emailVerifier,
        private UserRepositoryInterface $userRepository,
        private MailerInterface $mailer
    ) {}

    public function onUserRegistered(UserRegistered $event): void
    {
        $user = $this->userRepository->get($event->userId);

        // Envoyer l'e-mail de confirmation
        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new MimeEmail())
                ->to((string) $user->getEmail())
                ->subject('Veuillez confirmer votre adresse e-mail')
                ->html('<p>Veuillez confirmer votre adresse e-mail en cliquant sur le lien suivant : ...</p>') // Utiliser un template Twig en production
        );
    }
}
