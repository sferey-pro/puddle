<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Exception;

use App\Module\Auth\Domain\ValueObject\Hash;
use App\Module\Auth\Domain\ValueObject\OtpAttemptId;
use App\Module\Auth\Domain\ValueObject\UserIdentity;

/**
 * Exception métier unique pour l'entité OtpVerification.
 * Elle centralise toutes les erreurs possibles liées au processus de connexion par otp.
 */
final class OtpVerificationException extends \DomainException
{
    public const NOT_FOUND = 'OV-001';
    public const EXPIRED = 'OV-002';
    public const ALREADY_VERIFIED = 'OV-003';

    /**
     * Le constructeur est privé pour forcer l'utilisation des factory methods statiques.
     */
    private function __construct(string $message, private string $errorCode, private mixed $payload = null, ?\Throwable $previous = null)
    {
        parent::__construct(message: $message, previous: $previous);
    }

    public static function notFoundWithId(OtpAttemptId $id): self
    {
        return new self(\sprintf('Otp attempt with ID "%s" not found.', $id), self::NOT_FOUND);
    }

    public static function notFoundWithHash(Hash $hash): self
    {
        return new self(\sprintf('Otp attempt with Hash "%s" not found.', $hash), self::NOT_FOUND);
    }

    public static function invalidCode(UserIdentity $identity): self
    {
        return new self(\sprintf('The provided code "%s" is invalid.', $identity), self::NOT_FOUND);
    }

    public static function expired(): self
    {
        return new self('The phone verification has expired.', self::EXPIRED);
    }

    public static function alreadyVerified(): self
    {
        return new self('The phone verification has already been verified.', self::ALREADY_VERIFIED);
    }

    public static function tooManyAttempts(UserIdentity $identity): self
    {
        return new self(\sprintf('Too many verification attempts for this phone number: %s', $identity), self::ALREADY_VERIFIED);
    }

    public function payload(): mixed
    {
        return $this->payload;
    }

    public function errorCode(): string
    {
        return $this->errorCode;
    }
}
