<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain;

use App\Module\Auth\Domain\Exception\OtpVerificationException;
use App\Module\Auth\Domain\ValueObject\OtpAttemptId;
use App\Module\Auth\Domain\ValueObject\UserIdentity;

final class OneTimePasswordAttempt
{
    private const OTP_TTL_IN_SECONDS = 300; // 5 minutes (Pilier 3)
    private const MAX_VERIFICATION_ATTEMPTS = 5; // (Pilier 4)

    private function __construct(
        private readonly OtpAttemptId $id,
        private readonly UserIdentity $identity,
        private readonly string $hashedOtp, // Pilier 2: On ne stocke que le hash
        private readonly \DateTimeImmutable $expiresAt,
        private int $attempts = 0,
        private bool $isVerified = false,
    ) {
    }

    public static function request(
        OtpAttemptId $id,
        UserIdentity $identity,
        string $plainOtp,
        callable $hasher
    ): self {
        return new self(
            $id,
            $identity,
            $hasher($plainOtp),
            new \DateTimeImmutable('+' . self::OTP_TTL_IN_SECONDS . ' seconds')
        );
    }

    public function verify(string $submittedOtp, callable $verifier): void
    {
        if ($this->isVerified) {
            throw OtpVerificationException::alreadyVerified($this->identity);
        }

        if ($this->isExpired()) {
            throw OtpVerificationException::expired($this->identity);
        }

        if ($this->attempts >= self::MAX_VERIFICATION_ATTEMPTS) {
            throw OtpVerificationException::tooManyAttempts($this->identity);
        }

        if (!$verifier($submittedOtp, $this->hashedOtp)) {
            $this->attempts++;
            throw OtpVerificationException::invalidCode($this->identity);
        }

        $this->isVerified = true;
    }

    private function isExpired(): bool
    {
        return new \DateTimeImmutable() > $this->expiresAt;
    }
}
