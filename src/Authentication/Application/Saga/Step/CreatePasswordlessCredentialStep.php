<?php

namespace Authentication\Application\Saga\Step;

use Account\Registration\Domain\Saga\Process\RegistrationSagaProcess;
use Authentication\Application\Command\CreateMagicLink;
use Authentication\Domain\Model\AccessCredential\MagicLinkCredential;
use Authentication\Domain\Model\AccessCredential\OTPCredential;
use Authentication\Domain\Repository\AccessCredentialRepositoryInterface;
use Authentication\Domain\Service\TokenGeneratorInterface;
use Authentication\Domain\ValueObject\Token\OTPCode;
use Authentication\Infrastructure\Security\LoginLinkAdapter;
use Identity\Domain\ValueObject\EmailIdentity;
use Identity\Domain\ValueObject\PhoneIdentity;
use Kernel\Application\Bus\CommandBusInterface;
use Kernel\Application\Saga\Process\SagaProcessInterface;
use Kernel\Application\Saga\Step\Attribute\SagaStep;
use Kernel\Application\Saga\Step\SagaStepInterface;
use Psr\Log\LoggerInterface;

#[SagaStep('create_credential', 'registration')]
final class CreatePasswordlessCredentialStep implements SagaStepInterface
{
    public function __construct(
        private readonly AccessCredentialRepositoryInterface $credentialRepository,
        private readonly LoginLinkAdapter $loginLinkAdapter,
        private readonly CommandBusInterface $commandBus,
        private readonly LoggerInterface $logger
    ) {}

    public function execute(SagaProcessInterface $sagaProcess): void
    {
        if (!$sagaProcess instanceof RegistrationSagaProcess) {
            throw new \LogicException('Cette étape ne peut être exécutée que pour une RegistrationSagaProcess.');
        }

        $userId = $sagaProcess->userId();
        $identifier = $sagaProcess->identifier();

        $this->logger->info('Creating passwordless credentials', [
            'user_id' => (string) $userId,
            'identifier_type' => get_class($identifier)
        ]);

        try {
            // Créer le credential approprié selon le type d'identifier
            $command = match (true) {
                $identifier instanceof EmailIdentity => $command = new CreateMagicLink($userId, $identifier, 3600),
                $identifier instanceof PhoneIdentity => $command = new CreateOTP($userId, $identifier, 3600),
                default => throw new \LogicException(sprintf(
                    'Unsupported identifier type: %s',
                    get_class($identifier)
                ))
            };

            $this->commandBus->dispatch($command);

        } catch (\Exception $e) {
            $this->logger->error('Failed to create passwordless credential', [
                'user_id' => (string) $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function compensate(SagaProcessInterface $sagaProcess): void
    {
        if (!$sagaProcess instanceof RegistrationSagaProcess) {
            return;
        }

        $userId = $sagaProcess->userId();
        $identifier = $sagaProcess->identifier();

        $this->logger->info('Compensating passwordless credential creation', [
            'user_id' => (string) $userId
        ]);

        try {
            // Supprime le credential crée
            $credential = $this->credentialRepository->findByIdentifierAndUserId($identifier, $userId);

            $this->credentialRepository->remove($credential);
            $this->logger->debug('Removed credential', [
                'credential_id' => (string) $credential->id,
                'user_id' => (string) $userId
            ]);

            $this->logger->info('Passwordless credential compensation completed', [
                'user_id' => (string) $userId
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to compensate passwordless credential', [
                'user_id' => (string) $userId,
                'error' => $e->getMessage()
            ]);
            // On ne relance pas l'exception car la compensation doit être best-effort
        }
    }

    private function createMagicLinkCredential(RegistrationSagaProcess $sagaProcess): MagicLinkCredential
    {
        $userId = $sagaProcess->userId();
        $identifier = $sagaProcess->identifier();

        // Utiliser l'adapter Symfony pour créer un login link
        $loginLink = $this->loginLinkAdapter->createLoginLink(
            userIdentifier: $identifier->value(),
            lifetime: 3600 // 1 heure pour la première connexion
        );

        return MagicLinkCredential::create(
            userId: $userId,
            token: $loginLink->getToken(),
            hashedToken: hash('sha256', $loginLink->getToken()),
            expiresAt: new \DateTimeImmutable('+1 hour'),
            metadata: [
                'identifier' => $identifier->value(),
                'first_login' => true,
                'created_via' => 'registration_saga'
            ]
        );
    }

    private function createOTPCredential(RegistrationSagaProcess $sagaProcess): OTPCredential
    {
        $userId = $sagaProcess->userId();
        $identifier = $sagaProcess->identifier();

        // Générer un code OTP pour SMS
        $otpCode = OTPCode::generate();

        return OTPCredential::create(
            userId: $userId,
            code: $otpCode,
            hashedCode: password_hash($otpCode, PASSWORD_ARGON2ID),
            expiresAt: new \DateTimeImmutable('+10 minutes'),
            maxAttempts: 3,
            metadata: [
                'identifier' => $identifier->value(),
                'first_login' => true,
                'created_via' => 'registration_saga'
            ]
        );
    }
}
