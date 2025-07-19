<?php

declare(strict_types=1);

namespace SharedKernel\Domain\DTO\Authentication;

final readonly class AuthenticationAttemptDTO
{
    public function __construct(
        public \DateTimeImmutable $attemptedAt,
        public bool $success,
        public string $ipAddress,
        public string $userAgent,
        public ?string $failureReason,
        public string $method // password, oauth, magic-link, api-key
    ) {}
}
