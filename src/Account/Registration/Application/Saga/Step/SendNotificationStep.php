<?php

namespace Account\Registration\Application\Saga\Step;

/**
 * Étape 4 : Envoyer la notification (modifiée pour passwordless)
 */
final class SendNotificationStep extends AbstractSagaStep
{
    public function __construct(
        private readonly NotificationInterface $emailNotification,
        private readonly NotificationInterface $smsNotification,
        private readonly AccessCredentialRepositoryInterface $credentialRepository,
        private readonly LoginLinkAdapter $loginLinkAdapter,
        private readonly UrlGeneratorInterface $urlGenerator
    ) {}

    public function execute(RegistrationSagaProcess $process): StepResult
    {
        $channel = $process->channel();
        $identifier = $process->identifier();

        try {
            if ($process->isPasswordless()) {
                return $this->sendPasswordlessNotification($process);
            }

            // Ancien comportement pour non-passwordless
            return $this->sendTraditionalWelcomeEmail($process);

        } catch (\Exception $e) {
            return StepResult::failure(
                'Failed to send notification',
                ['error' => $e->getMessage()]
            );
        }
    }

    private function sendPasswordlessNotification(RegistrationSagaProcess $process): StepResult
    {
        $credentialId = $process->getCredentialId();
        if (!$credentialId) {
            return StepResult::failure('No credential found for notification');
        }

        $credential = $this->credentialRepository->findById($credentialId);
        if (!$credential) {
            return StepResult::failure('Credential not found');
        }

        $channel = $process->channel();
        $identifier = $process->identifier();

        if ($channel === 'email') {
            // Magic Link Email
            $magicLinkUrl = $this->loginLinkAdapter->generateMagicLinkUrl($credential);

            $message = new NotificationMessage(
                recipient: $identifier->value(),
                channel: 'email',
                template: 'registration/welcome_magic_link.html.twig',
                parameters: [
                    'subject' => 'Welcome to Puddle - Complete your registration',
                    'magic_link_url' => $magicLinkUrl,
                    'expires_in_minutes' => 30,
                ]
            );

            $this->emailNotification->send($message);

        } else {
            // OTP SMS
            $message = new NotificationMessage(
                recipient: $identifier->value(),
                channel: 'sms',
                template: 'registration_otp',
                parameters: [
                    'code' => $credential->getCode()->toString(),
                    'expires_in' => '10 minutes'
                ]
            );

            $this->smsNotification->send($message);
        }

        return StepResult::success([
            'notification_sent' => true,
            'channel' => $channel
        ]);
    }
}
