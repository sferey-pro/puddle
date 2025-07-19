<?php

declare(strict_types=1);

namespace Authentication\Domain\Model;

use Authentication\Domain\Enum\FailureReason;
use Authentication\Domain\Enum\LoginMethod;
use Authentication\Domain\Model\Identity\LoginAttemptId;
use Authentication\Domain\Model\Identity\SessionId;
use Identity\Domain\ValueObject\Identifier;
use SharedKernel\Domain\ValueObject\Identity\UserId;

final class LoginAttempt
{
    private(set) LoginAttemptId $id;
    private(set) ?UserId $userId;
    private(set) Identifier $identifier;
    private(set) LoginMethod $method;
    private(set) bool $successful;
    private(set) ?FailureReason $failureReason;
    private(set) string $ipAddress;
    private(set) string $userAgent;
    private(set) ?string $country;
    private(set) ?string $city;
    private(set) \DateTimeImmutable $attemptedAt;
    private(set) ?SessionId $resultingSessionId;

    private function __construct(
        LoginAttemptId $id,
        Identifier $identifier,
        LoginMethod $method,
        string $ipAddress,
        string $userAgent
    ) {
        $this->id = $id;
        $this->identifier = $identifier;
        $this->method = $method;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->attemptedAt = new \DateTimeImmutable();
        $this->successful = false;
        $this->userId = null;
        $this->failureReason = null;
        $this->country = null;
        $this->city = null;
        $this->resultingSessionId = null;
    }

    public static function record(
        Identifier $identifier,
        LoginMethod $method,
        string $ipAddress,
        string $userAgent
    ): self {
        return new self(
            LoginAttemptId::generate(),
            $identifier,
            $method,
            $ipAddress,
            $userAgent
        );
    }

    public function markSuccessful(UserId $userId, SessionId $sessionId): void
    {
        if ($this->successful) {
            throw new \DomainException('Login attempt already marked as successful');
        }

        $this->successful = true;
        $this->userId = $userId;
        $this->resultingSessionId = $sessionId;
    }

    public function markFailed(FailureReason $reason, ?UserId $userId = null): void
    {
        if ($this->successful) {
            throw new \DomainException('Cannot mark successful attempt as failed');
        }

        $this->failureReason = $reason;
        $this->userId = $userId;
    }

    public function enrichWithGeoData(string $country, string $city): void
    {
        $this->country = $country;
        $this->city = $city;
    }

    public function isRecentAttempt(int $minutes = 30): bool
    {
        $threshold = new \DateTimeImmutable("-{$minutes} minutes");
        return $this->attemptedAt > $threshold;
    }

    public function isSuspiciousLocation(string $expectedCountry): bool
    {
        return $this->country !== null && $this->country !== $expectedCountry;
    }

    public function isFromSameIp(string $ipAddress): bool
    {
        return $this->ipAddress === $ipAddress;
    }
}
