<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Module\Auth\Domain\Repository\PasswordResetRequestRepositoryInterface;
use App\Module\Auth\Domain\Service\PasswordResetTokenGeneratorInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * Rôle : Gère la création d'un token de configuration de mot de passe et envoie
 * l'e-mail d'invitation à l'utilisateur. Réutilise la logique de réinitialisation de mot de passe.
 */
#[AsCommandHandler]
final readonly class RequestInitialPasswordSetupHandler
{
    public function __construct(
        private PasswordResetRequestRepositoryInterface $repository,
        private PasswordResetTokenGeneratorInterface $tokenGenerator,
        private MailerInterface $mailer
    ) {}

    public function __invoke(RequestInitialPasswordSetup $command): void
    {
        $passwordResetRequest = $this->tokenGenerator->generate($command->userId);
        $this->repository->save($passwordResetRequest);

        // TODO: Utiliser un template Twig pour cet e-mail
        $email = (new Email())
            ->to((string) $command->email)
            ->subject('Bienvenue ! Configurez votre compte.')
            ->html(sprintf(
                '<p>Bonjour,</p><p>Un compte a été créé pour vous. Veuillez cliquer sur le lien ci-dessous pour définir votre mot de passe et activer votre compte :</p><a href="/reset-password/%s">Configurer mon mot de passe</a>',
                $passwordResetRequest->getToken()
            ));

        $this->mailer->send($email);
    }
}
