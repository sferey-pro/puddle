<?php

declare(strict_types=1);

/**
 * Handler pour la demande de magic link
 */
namespace Authentication\Application\Command;

use Authentication\Application\Notifier\CustomLoginLinkNotification;
use Authentication\Application\Notifier\LoginEmailNotification;
use Authentication\Domain\Exception\TooManyAttemptsException;
use Authentication\Domain\Repository\AccessCredentialRepositoryInterface;
use Authentication\Infrastructure\Security\LoginLinkAdapter;
use Identity\Domain\ValueObject\Identifier;
use Kernel\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use SharedKernel\Domain\Service\AccountRegistrationContextInterface;
use SharedKernel\Domain\Service\IdentityContextInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

#[AsCommandHandler]
final readonly class RequestMagicLinkHandler
{
    public function __construct(
        private AccountRegistrationContextInterface $accountRegistrationContext,
        private IdentityContextInterface $identityContext,
        private AccessCredentialRepositoryInterface $credentialRepository,
        private LoginLinkAdapter $loginLinkAdapter,
        private NotifierInterface $notifier
    ) {}

    public function __invoke(RequestMagicLink $command): void
    {
        $identifier = $command->email;

        // 1. Vérifier le rate limiting
        $recentAttempts = $this->credentialRepository->countRecentAttempts(
            $identifier,
            new \DateInterval('PT5M')
        );

        if ($recentAttempts >= 3) {
            throw TooManyAttemptsException::forEmail(
                $identifier->value(),
                60 // Wait 60 seconds
            );
        }

        // 2. Chercher un compte existant
        $existingUserId = $this->identityContext->findUserIdByIdentifier($identifier->value());

        $metadata = [
            'ip_address' => $command->ipAddress,
            'user_agent' => $command->userAgent,
            'requested_at' => (new \DateTimeImmutable())->format('c'),
        ];

        if ($existingUserId === null) {
            // 3a. Nouveau compte - démarrer la Saga Registration
            $this->accountRegistrationContext->initiateRegistration($identifier->value(), $command->ipAddress);
        } else {
            // 3b. Compte existant - envoyer le magic link
            $this->sendMagicLinkToExistingAccount($existingUserId, $command);
        }
    }

    private function sendMagicLinkToExistingAccount($userId, RequestMagicLink $command): void
    {
        // Créer le credential avec Symfony LoginLink
        $loginLinkDetails = $this->loginLinkAdapter->createLoginLink($userId);

        // Envoyer l'email
        $notification = new LoginEmailNotification(
            $loginLinkDetails,
            'Votre lien de connexion Puddle', // email subject
            $command->ipAddress
        );

        $recipient = new Recipient((string) $command->email);
        $this->notifier->send($notification, $recipient);
    }
}
