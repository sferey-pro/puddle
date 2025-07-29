<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Security\OTP;

use Authentication\Domain\ValueObject\Token\OTPCode;
use Identity\Domain\ValueObject\Identifier;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Détails d'un code OTP.
 *
 * Contient toutes les informations nécessaires pour créer et vérifier un OTP.
 */
final readonly class OTPDetails
{
    private function __construct(
        private(set) OTPCode $code,
        private(set) string $hashedCode,
        private(set) Identifier $identifier,
        private(set) UserId $userId,
        private(set) \DateTimeImmutable $expiresAt,
        private(set) int $maxAttempts,
        private(set) array $metadata,
        private(set) ?string $signature = null
    ) {}

    public static function create(
        OTPCode $code,
        Identifier $identifier,
        UserId $userId,
        \DateTimeImmutable $expiresAt,
        int $maxAttempts = 3,
        array $metadata = []
    ): self {
        return new self(
            code: $code,
            hashedCode: password_hash($code, PASSWORD_ARGON2ID),
            identifier: $identifier,
            userId: $userId,
            expiresAt: $expiresAt,
            maxAttempts: $maxAttempts,
            metadata: $metadata,
            signature: null
        );
    }

    public static function fromHash(
        string $hashedCode,
        Identifier $identifier,
        UserId $userId,
        \DateTimeImmutable $expiresAt,
        int $maxAttempts,
        array $metadata,
        ?string $signature = null
    ): self {
        return new self(
            code: '', // Code non stocké pour sécurité
            hashedCode: $hashedCode,
            identifier: $identifier,
            userId: $userId,
            expiresAt: $expiresAt,
            maxAttempts: $maxAttempts,
            metadata: $metadata,
            signature: $signature
        );
    }

    public function withSignature(string $signature): self
    {
        return new self(
            code: $this->code,
            hashedCode: $this->hashedCode,
            identifier: $this->identifier,
            userId: $this->userId,
            expiresAt: $this->expiresAt,
            maxAttempts: $this->maxAttempts,
            metadata: $this->metadata,
            signature: $signature
        );
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTimeImmutable();
    }

    public function verifyCode(string $code): bool
    {
        return password_verify($code, $this->hashedCode);
    }

    public function toArray(): array
    {
        return [
            'phone' => $this->identifier->value(),
            'user_id' => $this->userId,
            'expires_at' => $this->expiresAt->format('c'),
            'max_attempts' => $this->maxAttempts,
            'metadata' => $this->metadata,
        ];
    }
}
