<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Security;

use Authentication\Infrastructure\Security\OTP\OTPDetails;
use Authentication\Infrastructure\Security\OTP\OTPHandlerInterface;
use Identity\Domain\ValueObject\PhoneIdentity;
use Kernel\Application\Notifier\NotifierService;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Adapter pour intégrer le système OTP dans l'architecture Symfony.
 *
 * RESPONSABILITÉS :
 * - Orchestrer la création et l'envoi des OTP
 * - Adapter les appels pour les différents contextes
 * - Gérer l'envoi des notifications
 */
final class OTPAdapter
{
    public function __construct(
        private readonly OTPHandlerInterface $otpHandler,
        private readonly NotifierService $notifier
    ) {}

    /**
     * Crée et envoie un OTP pour un utilisateur existant.
     */
    public function createOTP(
        UserId $userId,
        PhoneIdentity $phoneIdentity,
        array $context = []
    ): OTPDetails {
        // Créer l'OTP
        $otpDetails = $this->otpHandler->createOTP(
            phoneIdentity: $phoneIdentity,
            userId: $userId,
            metadata: array_merge($context, [
                'type' => 'authentication',
            ])
        );

        return $otpDetails;
    }

    /**
     * Crée et envoie un OTP pour l'inscription.
     */
    public function createOTPForRegistration(
        PhoneIdentity $phoneIdentity,
        array $context = []
    ): OTPDetails {
        // Créer un UserId temporaire pour la session d'inscription
        $tempUserId = UserId::generate();

        // Créer l'OTP
        $otpDetails = $this->otpHandler->createOTP(
            phoneIdentity: $phoneIdentity,
            userId: $tempUserId,
            metadata: array_merge($context, [
                'type' => 'registration'
            ])
        );

        return $otpDetails;
    }

    /**
     * Consomme un OTP et retourne les détails.
     */
    public function consumeOTP(
        PhoneIdentity $phoneIdentity,
        string $code
    ): OTPDetails {
        return $this->otpHandler->consumeOTP($phoneIdentity, $code);
    }

    /**
     * Vérifie si un OTP est valide.
     */
    public function isValidOTP(
        PhoneIdentity $phoneIdentity,
        string $code
    ): bool {
        return $this->otpHandler->verifyOTP($phoneIdentity, $code);
    }

    private function calculateMinutesUntilExpiry(\DateTimeImmutable $expiresAt): int
    {
        $now = new \DateTimeImmutable();
        $diff = $now->diff($expiresAt);

        return ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
    }
}
