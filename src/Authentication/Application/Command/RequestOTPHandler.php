<?php

declare(strict_types=1);

namespace Authentication\Application\Command;

use Authentication\Application\Notifier\LoginSmsNotification;
use Authentication\Application\Notifier\OtpNotification;
use Authentication\Domain\Model\AccessCredential\OTPCredential;
use Authentication\Domain\Repository\AccessCredentialRepositoryInterface;
use Authentication\Domain\Service\TokenGeneratorInterface;
use Authentication\Domain\Exception\TooManyAttemptsException;
use Authentication\Infrastructure\Security\OTPAdapter;
use Identity\Domain\ValueObject\Identifier;
use Identity\Domain\ValueObject\PhoneIdentity;
use Kernel\Application\Notifier\NotifierService;
use Kernel\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use SharedKernel\Domain\Service\AccountRegistrationContextInterface;
use SharedKernel\Domain\Service\IdentityContextInterface;
use SharedKernel\Domain\ValueObject\Identity\UserId;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

#[AsCommandHandler]
final readonly class RequestOTPHandler
{
    public function __construct(
        private AccountRegistrationContextInterface $accountRegistrationContext,
        private IdentityContextInterface $identityContext,
        private AccessCredentialRepositoryInterface $credentialRepository,
        private OTPAdapter $otpAdapter,
        private NotifierInterface $notifier
    ) {}

    public function __invoke(RequestOTP $command): void
    {
        $identifier = $command->identifier;

        // 1. Vérifier le rate limiting
        $recentAttempts = $this->credentialRepository->countRecentAttempts(
            $identifier,
            new \DateInterval('PT2M') // 2 minutes pour SMS
        );

        if ($recentAttempts >= 3) {
            throw TooManyAttemptsException::forPhone(
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
            // 3b. Compte existant - envoyer le code OTP
            $this->sendOTPToExistingAccount($existingUserId, $identifier, $command);
        }
    }

    private function sendOTPToExistingAccount(
        UserId $userId,
        Identifier $identifier,
        RequestOTP $command
    ): void {

        $otpDetails = $this->otpAdapter->createOTP($userId, $identifier);

        // Envoyer le code OTP
        $notification = new LoginSmsNotification(
            $otpDetails->code->value(),
            $identifier->value(),
            $otpDetails->code->expiresAt()
        );

        $recipient = new Recipient((string) $identifier->value());
        $this->notifier->send($notification, $recipient);
    }
}
