<?php

declare(strict_types=1);

namespace Authentication\Domain\Model\Model;

use App\Authentication\Domain\Model\ValueObject\SessionToken;
use Authentication\Domain\Model\Event\SessionCreatedEvent;
use Authentication\Domain\Model\Event\SessionInvalidatedEvent;
use Authentication\Domain\Model\Event\SessionRefreshedEvent;
use Authentication\Domain\Model\Identity\SessionId;
use Authentication\Domain\Model\ValueObject\DeviceFingerprint;
use Kernel\Domain\Aggregate\AggregateRoot;
use SharedKernel\Domain\ValueObject\Identity\UserId;

final class Session extends AggregateRoot
{
    private(set) SessionId $id;
    private(set) UserId $userId;
    private(set) SessionToken $token;
    private(set) string $ipAddress;
    private(set) string $userAgent;
    private(set) \DateTimeImmutable $createdAt;
    private(set) \DateTimeImmutable $lastActivityAt;
    private(set) \DateTimeImmutable $expiresAt;
    private(set) bool $is2FAVerified;
    private(set) bool $isActive;
    private(set) ?string $invalidationReason;

    private function __construct(
        SessionId $id,
        UserId $userId,
        SessionToken $token,
        string $ipAddress,
        string $userAgent,
        int $ttlSeconds
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->token = $token;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->createdAt = new \DateTimeImmutable();
        $this->lastActivityAt = new \DateTimeImmutable();
        $this->expiresAt = $this->createdAt->add(new \DateInterval("PT{$ttlSeconds}S"));
        $this->is2FAVerified = false;
        $this->isActive = true;
        $this->invalidationReason = null;
    }

    public static function create(
        UserId $userId,
        string $ipAddress,
        string $userAgent,
        int $ttlSeconds = 3600
    ): self {
        $session = new self(
            SessionId::generate(),
            $userId,
            SessionToken::generate(),
            $ipAddress,
            $userAgent,
            $ttlSeconds
        );

        $session->raise(new SessionCreatedEvent(
            sessionId: $session->id,
            userId: $session->userId,
            ipAddress: $session->ipAddress,
            createdAt: $session->createdAt
        ));

        return $session;
    }

    public function verify2FA(): void
    {
        if ($this->is2FAVerified) {
            throw new \DomainException('Session already 2FA verified');
        }

        if (!$this->isActive()) {
            throw new \DomainException('Cannot verify 2FA on inactive session');
        }

        $this->is2FAVerified = true;
        $this->extendSession(7200); // Extend session after 2FA
    }

    public function refresh(): void
    {
        if (!$this->isActive()) {
            throw new \DomainException('Cannot refresh inactive session');
        }

        if ($this->isExpired()) {
            throw new \DomainException('Cannot refresh expired session');
        }

        $oldToken = $this->token;
        $this->token = SessionToken::generate();
        $this->lastActivityAt = new \DateTimeImmutable();

        $this->raise(new SessionRefreshedEvent(
            sessionId: $this->id,
            oldToken: $oldToken->toString(),
            newToken: $this->token->toString(),
            refreshedAt: $this->lastActivityAt
        ));
    }

    public function recordActivity(): void
    {
        if (!$this->isActive()) {
            return;
        }

        $this->lastActivityAt = new \DateTimeImmutable();
    }

    public function invalidate(string $reason = 'manual_logout'): void
    {
        if (!$this->isActive) {
            return;
        }

        $this->isActive = false;
        $this->invalidationReason = $reason;

        $this->raise(new SessionInvalidatedEvent(
            sessionId: $this->id,
            userId: $this->userId,
            reason: $reason,
            invalidatedAt: new \DateTimeImmutable()
        ));
    }

    public function extendSession(int $additionalSeconds): void
    {
        if (!$this->isActive()) {
            throw new \DomainException('Cannot extend inactive session');
        }

        $this->expiresAt = $this->expiresAt->add(
            new \DateInterval("PT{$additionalSeconds}S")
        );
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTimeImmutable();
    }

    public function isActive(): bool
    {
        return $this->isActive && !$this->isExpired();
    }

    public function belongsToAccount(UserId $userId): bool
    {
        return $this->userId->equals($userId);
    }

    public function getTimeUntilExpiry(): \DateInterval
    {
        return $this->expiresAt->diff(new \DateTimeImmutable());
    }
}
