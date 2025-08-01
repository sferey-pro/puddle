<?php

namespace Authentication\Infrastructure\Port\Out\DTO;

final readonly class UserIdentifierDTO
{
    public function __construct(
        public string $type,
        public string $value,
        public bool $isVerified,
        public bool $isPrimary,
        public \DateTimeImmutable $addedAt
    ) {}
}
