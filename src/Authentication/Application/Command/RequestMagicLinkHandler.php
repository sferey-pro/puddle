<?php

declare(strict_types=1);

/**
 * Handler pour la demande de magic link
 */
namespace Authentication\Application\Command;

use Authentication\Application\Notifier\CustomLoginLinkNotification;
use Authentication\Domain\Exception\TooManyAttemptsException;
use Authentication\Domain\Repository\AccessCredentialRepositoryInterface;
use Authentication\Infrastructure\Security\SymfonyLoginLinkAdapter;
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
        private SymfonyLoginLinkAdapter $loginLinkAdapter,
        private NotifierInterface $notifier
    ) {}

    public function __invoke(RequestMagicLink $command): void
    {
        $identifier = $command->email;

        // 1. Vérifier le rate limiting
        // $recentAttempts = $this->credentialRepository->countRecentAttempts(
        //     $identifier,
        //     new \DateInterval('PT5M')
        // );

        // if ($recentAttempts >= 3) {
        //     throw new TooManyAttemptsException(
        //         'Please wait a few minutes before requesting another magic link.'
        //     );
        // }

        // 2. Chercher un compte existant
        $account = $this->identityContext->findUserIdByIdentifier($identifier->value());

        if ($account === null) {
            // 3a. Nouveau compte - démarrer la Saga Registration
            $this->accountRegistrationContext->initiateRegistration($identifier->value(), $command->ipAddress);
        } else {
            // 3b. Compte existant - envoyer le magic link
            $this->sendMagicLinkToExistingAccount($account, $command);
        }
    }

    private function sendMagicLinkToExistingAccount($account): void
    {
        // Créer le credential avec Symfony LoginLink
        $loginLinkDetails = $this->loginLinkAdapter->createMagicLinkForAccount($account);

        // Envoyer l'email
        $notification = new CustomLoginLinkNotification(
            $loginLinkDetails,
            'Votre lien de connexion Puddle' // email subject
        );

        $recipient = new Recipient((string) $account->email);
        $this->notifier->send($notification, $recipient);
    }
}
