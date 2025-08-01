<?php

namespace Authentication\Application\Query\DTO;

final readonly class CredentialDTO
{
    public function __construct(
        public string $id,
        public string $type, // 'magic_link', 'otp'
        public string $identifier,
        public \DateTimeImmutable $createdAt,
        public ?\DateTimeImmutable $expiresAt,
        public ?\DateTimeImmutable $lastUsedAt,
        public int $usageCount,
        public string $status // 'active', 'expired', 'revoked'
    ) {}
}
