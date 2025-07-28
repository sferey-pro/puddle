<?php

declare(strict_types=1);

namespace Authentication\Application\Command;

use Authentication\Domain\Repository\AccessCredentialRepositoryInterface;
use SharedKernel\Domain\Service\AccountRegistrationContextInterface;
use SharedKernel\Domain\Service\AuthenticationContextInterface;
use SharedKernel\Domain\Service\IdentityContextInterface;

#[AsCommandHandler]
final readonly class RequestOTPHandler
{

    public function __construct(
        private AccountRegistrationContextInterface $accountRegistrationContext,
        private AuthenticationContextInterface $authenticationContext,
        private IdentityContextInterface $identityContext,
        private AccessCredentialRepositoryInterface $credentialRepository,
        private TokenGeneratorInterface $tokenGenerator,
        private NotificationInterface $smsNotification,
        private CommandBusInterface $commandBus,
        private EventBusInterface $eventBus
    ) {}

    public function __invoke(RequestOTP $command): void
    {
        // 1. Normaliser le numéro
        $normalizedPhone = $this->normalizePhoneNumber($command->phoneNumber);

        // 2. Rate limiting
        $recentAttempts = $this->credentialRepository->countRecentAttempts(
            $normalizedPhone,
            new \DateInterval('PT1M') // 1 minute pour OTP
        );

        if ($recentAttempts >= 3) {
            throw new TooManyAttemptsException(
                'Please wait before requesting another code.'
            );
        }

        // 3. Chercher un compte
        $account = $this->accountRepository
            ->withPhone($normalizedPhone)
            ->notDeleted()
            ->findOne();

        if ($account === null) {
            // Nouveau compte
            $this->accountRegistrationContext->initiateRegistration($identifier->value(), $command->ipAddress);
        } else {
            // Compte existant
            $this->sendOTPToExistingAccount($account, $command);
        }
    }

    private function sendOTPToExistingAccount($account, $command): void
    {
        // Générer OTP
        $otpCode = OTPCode::generate();

        // Créer credential
        $credential = OTPCredential::create(
            identifier: $this->normalizePhoneNumber($command->phoneNumber),
            code: $otpCode,
            validity: new \DateInterval('PT5M')
        );

        $credential->attachToUser($account->getId());
        $this->credentialRepository->save($credential);

        // Envoyer SMS
        $this->smsNotification->send(new NotificationMessage(
            recipient: $command->phoneNumber,
            channel: 'sms',
            template: 'otp_template',
            parameters: [
                'code' => $otpCode->toString(),
                'expires_in' => '5 minutes'
            ]
        ));

        // Event
        $this->eventBus->publish(new OTPRequested(
            userId: $account->getId(),
            phoneNumber: $command->phoneNumber
        ));
    }
}
