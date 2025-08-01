<?php

declare(strict_types=1);

/**
 * Handler pour la demande de magic link
 */
namespace Authentication\Application\Command;

use Account\Core\Domain\Model\Account;
use Account\Registration\Domain\Model\RegistrationRequest;
use Account\Registration\Domain\Specification\CanRegisterSpecification;
use Authentication\Application\Notifier\CustomLoginLinkNotification;
use Authentication\Application\Notifier\LoginEmailNotification;
use Authentication\Application\Service\LoginValidationService;
use Authentication\Domain\Exception\AuthenticationException;
use Authentication\Domain\Exception\TooManyAttemptsException;
use Authentication\Domain\Model\LoginRequest;
use Authentication\Domain\Repository\AccessCredentialRepositoryInterface;
use Authentication\Domain\Repository\LoginAttemptRepositoryInterface;
use Authentication\Domain\ValueObject\LoginAttemptHistory;
use Authentication\Infrastructure\Security\LoginLinkAdapter;
use Identity\Domain\ValueObject\EmailIdentity;
use Identity\Domain\ValueObject\Identifier;
use Kernel\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use SharedKernel\Domain\Service\AccountContextInterface;
use SharedKernel\Domain\Service\AccountRegistrationContextInterface;
use SharedKernel\Domain\Service\IdentityContextInterface;
use SharedKernel\Domain\ValueObject\Identity\UserId;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

#[AsCommandHandler]
final readonly class RequestMagicLinkHandler
{
    public function __construct(
        private AccountRegistrationContextInterface $accountRegistrationContext,
        private LoginValidationService $loginValidation,
        private IdentityContextInterface $identityContext,
        private CanRegisterSpecification $canRegister,
        private AccountContextInterface $accountContext,
        private AccessCredentialRepositoryInterface $credentialRepository,
        private LoginAttemptRepositoryInterface $attemptRepository,
        private LoginLinkAdapter $loginLinkAdapter,
        private NotifierInterface $notifier
    ) {}

    public function __invoke(RequestMagicLink $command): void
    {
        $identifier = $command->email;

        // ===== DÉTECTION : INSCRIPTION VS CONNEXION =====
        $existingUserId = $this->identityContext->findUserIdByIdentifier($identifier->value());

        if ($existingUserId === null) {
            // 🆕 NOUVEAU COMPTE → PROCESSUS D'INSCRIPTION
            $this->handleRegistration($identifier, $command);
        } else {
            // 🔑 COMPTE EXISTANT → PROCESSUS DE CONNEXION
            $this->handleLogin($identifier, $existingUserId, $command);
        }
    }

    // ===== PROCESSUS D'INSCRIPTION =====
    private function handleRegistration(EmailIdentity $identifier, RequestMagicLink $command): void
    {
        // Construction de la demande d'inscription
        $registrationRequest = new RegistrationRequest(
            identifier: $identifier,
            userId: UserId::generate(), // Nouveau UserId
            metadata: [
                'ip_address' => $command->ipAddress,
                'user_agent' => $command->userAgent
            ]
        );

        // ✅ SPECIFICATIONS DE REGISTRATION
        if (!$this->canRegister->isSatisfiedBy($registrationRequest)) {
            throw AuthenticationException::registrationNotAllowed(
                $this->canRegister->failureReason()
            );
        }

        // Démarrer le Saga de Registration
        $this->accountRegistrationContext->initiateRegistration(
            $identifier->value(),
            $command->ipAddress
        );
    }

    // ===== PROCESSUS DE CONNEXION =====
    private function handleLogin(EmailIdentity $identifier, UserId $userId, RequestMagicLink $command): void
    {
        // Charger les tentatives récentes depuis le repository existant
        $recentAttemptsByIdentifier = $this->attemptRepository->findRecentByIdentifier($identifier, 15);

        $recentAttemptsByIp = $this->attemptRepository->findRecentByIp($command->ipAddress, 15);

        // Construction de la demande de connexion
        $loginRequest = new LoginRequest(
            identifier: $identifier,
            account: $this->loadAccount($userId),
            ipAddress: $command->ipAddress,
            userAgent: $command->userAgent,
            requestedAt: new \DateTimeImmutable(),
            recentAttemptsByIdentifier: $recentAttemptsByIdentifier,
            recentAttemptsByIp: $recentAttemptsByIp,
        );

        // ✅ SPECIFICATIONS D'AUTHENTICATION
        $validationResult = $this->loginValidation->validateLoginRequest($loginRequest);

        if (!$validationResult->isValid) {
            throw AuthenticationException::loginNotAllowed(
                $validationResult->errorCode,
                $validationResult->errorMessage
            );
        }

        // Générer et envoyer le Magic Link
        $this->sendMagicLink($identifier, $userId, $command);
    }

    // ===== HELPERS PRIVÉS =====
    private function sendMagicLink(EmailIdentity $identifier, UserId $userId, RequestMagicLink $command): void
    {
        $loginDetails = $this->loginLinkAdapter->createLoginLink($userId);

        $notification = new LoginEmailNotification(
            $loginDetails,
            'Votre lien de connexion Puddle',
            $command->ipAddress
        );

        $recipient = new Recipient($identifier->value());
        $this->notifier->send($notification, $recipient);
    }

    private function loadAccount(UserId $userId): ?Account
    {
        // Charger le compte pour les validations Authentication
        return $this->accountContext->ofId($userId);
    }
}
