<?php

namespace Authentication\Application\Saga\Step;

use Account\Registration\Domain\Saga\Process\RegistrationSagaProcess;
use Authentication\Domain\Model\AccessCredential\MagicLinkCredential;
use Authentication\Domain\Model\AccessCredential\OTPCredential;
use Authentication\Domain\Repository\AccessCredentialRepositoryInterface;
use Authentication\Domain\Service\TokenGeneratorInterface;
use Authentication\Infrastructure\Security\SymfonyLoginLinkAdapter;
use Kernel\Application\Saga\Step\AbstractSagaStep;
use Kernel\Application\Saga\Step\StepResult;

/**
 * Étape 3.5 : Créer le credential d'authentification
 */
final class CreatePasswordlessCredentialStep extends AbstractSagaStep
{
    public function __construct(
        private readonly AccessCredentialRepositoryInterface $credentialRepository,
        private readonly TokenGeneratorInterface $tokenGenerator,
        private readonly SymfonyLoginLinkAdapter $loginLinkAdapter
    ) {}

    public function execute(RegistrationSagaProcess $process): StepResult
    {
        if (!$process->isPasswordless()) {
            return StepResult::skip('Not a passwordless registration');
        }

        $userId = UserId::fromString($process->context('userId'));
        $identifier = $process->identifier();
        $channel = $process->channel();

        try {
            if ($channel === 'email') {
                // Créer Magic Link
                $token = MagicLinkToken::fromString(
                    $this->tokenGenerator->generateMagicLinkToken()
                );

                $credential = MagicLinkCredential::create(
                    identifier: $identifier->value(),
                    token: $token,
                    validity: new \DateInterval('PT30M') // 30 min pour inscription
                );

                $credential->attachToUser($userId);

            } else {
                // Créer OTP
                $code = OTPCode::fromString(
                    $this->tokenGenerator->generateOTPCode()
                );

                $credential = OTPCredential::create(
                    identifier: $identifier->value(),
                    code: $code,
                    validity: new \DateInterval('PT10M') // 10 min pour OTP
                );

                $credential->attachToUser($userId);
            }

            $this->credentialRepository->save($credential);

            // Attacher au process
            $process->attachCredential($credential->getId());

            return StepResult::success([
                'credential_id' => $credential->getId(),
                'credential_type' => $credential->getType()->toString()
            ]);

        } catch (\Exception $e) {
            return StepResult::failure(
                'Failed to create authentication credential',
                ['error' => $e->getMessage()]
            );
        }
    }

    public function compensate(RegistrationSagaProcess $process): StepResult
    {
        $credentialId = $process->getCredentialId();

        if (!$credentialId) {
            return StepResult::success();
        }

        try {
            $credential = $this->credentialRepository->findById($credentialId);
            if ($credential) {
                $this->credentialRepository->remove($credential);
            }

            return StepResult::success(['credential_removed' => true]);

        } catch (\Exception $e) {
            return StepResult::failure(
                'Failed to remove credential',
                ['error' => $e->getMessage()]
            );
        }
    }
}
