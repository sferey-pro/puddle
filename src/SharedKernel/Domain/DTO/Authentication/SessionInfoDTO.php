<?php

declare(strict_types=1);

namespace SharedKernel\Domain\DTO\Authentication;

use SharedKernel\Domain\ValueObject\Identity\UserId;

final readonly class SessionInfoDTO
{
    public function __construct(
        public string $sessionId,
        public UserId $userId,
        public string $ipAddress,
        public string $userAgent,
        public \DateTimeImmutable $createdAt,
        public \DateTimeImmutable $lastActivityAt,
        public \DateTimeImmutable $expiresAt,
        public bool $is2FAVerified
    ) {}

    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTimeImmutable();
    }

    public function getTimeRemaining(): \DateInterval
    {
        return $this->expiresAt->diff(new \DateTimeImmutable());
    }
}
