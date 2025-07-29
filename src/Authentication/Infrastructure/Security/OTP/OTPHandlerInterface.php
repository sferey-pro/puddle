<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Security\OTP;

use Identity\Domain\ValueObject\PhoneIdentity;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Interface pour la gestion des codes OTP.
 *
 * Équivalent à LoginLinkHandlerInterface de Symfony.
 */
interface OTPHandlerInterface
{
    /**
     * Crée un nouveau code OTP.
     */
    public function createOTP(
        PhoneIdentity $phoneIdentity,
        UserId $userId,
        array $metadata = [],
        ?\DateTimeImmutable $expiresAt = null
    ): OTPDetails;

    /**
     * Consomme (vérifie et invalide) un code OTP.
     *
     * @throws InvalidOTPException Si le code est invalide, expiré ou déjà utilisé
     */
    public function consumeOTP(
        PhoneIdentity $phoneIdentity,
        string $code
    ): OTPDetails;

    /**
     * Vérifie si un code OTP est valide sans le consommer.
     */
    public function verifyOTP(
        PhoneIdentity $phoneIdentity,
        string $code
    ): bool;
}
