<?php

declare(strict_types=1);

namespace Authentication\Domain\Model;

use Authentication\Domain\Enum\CredentialType;
use Authentication\Domain\Model\Identity\CredentialId;
use Authentication\Domain\ValueObject\Token;
use Identity\Domain\Model\ValueObject\Identifier;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Interface unifiée pour tous les types de credentials
 */
interface AccessCredential
{
    public CredentialId $id { get; }

    public ?UserId $userId { get; set; }
    public Identifier $identifier { get; }
    public Token $token { get; }

    public \DateTimeInterface $createdAt { get; }
    public \DateTimeInterface $expiresAt { get; }

    public function getType(): CredentialType;

    public function isValid(): bool;
    public function isExpired(\DateTimeInterface $now): bool;

    public function markAsUsed(\DateTimeInterface $when): void;
}
