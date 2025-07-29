<?php

declare(strict_types=1);

namespace Authentication\Domain\Model\AccessCredential;

use Authentication\Domain\Enum\CredentialType;
use Authentication\Domain\Model\Identity\CredentialId;
use Authentication\Domain\ValueObject\Token\OtpCode;
use Authentication\Infrastructure\Security\OTP\OTPDetails;
use Identity\Domain\ValueObject\Identifier;
use Kernel\Application\Clock\SystemTime;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Credential pour authentification par OTP
 */
final class OTPCredential extends AbstractAccessCredential
{
    private int $attempts = 0;
    private const MAX_ATTEMPTS = 3;

    public static function create(
        Identifier $identifier,
        OtpCode $token,
        ?\DateTimeImmutable $expiresAt = null
    ): static {
        $now = SystemTime::now();

        return new static(
            id: CredentialId::generate(),
            identifier: $identifier,
            token: $token,
            createdAt: $now,
            expiresAt: $expiresAt
        );
    }

    /**
     * Crée un OTPCredential à partir d'OTPDetails.
     */
    public static function fromDetails(OTPDetails $details): self
    {
        return self::create(
            identifier: $details->identifier,
            token: $details->code,
            expiresAt: $details->expiresAt,
        );
    }

    public function attachToUser(UserId $userId): void
    {
        $this->userId = $userId;
    }

    public function getType(): CredentialType
    {
        return CredentialType::OTP;
    }

    public function isValid(): bool
    {
        return $this->usedAt === null
            && !$this->isExpired(new \DateTimeImmutable())
            && $this->attempts < static::MAX_ATTEMPTS;
    }

    public function verifyOtp(string $code): bool
    {
        $this->attempts++;

        if ($this->attempts > static::MAX_ATTEMPTS) {
            throw new \DomainException('Too many attempts');
        }

        return $this->token->matches($code);
    }
}
