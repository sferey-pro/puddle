<?php

declare(strict_types=1);

namespace Authentication\Domain\Model\AccessCredential;

use Authentication\Domain\Enum\CredentialType;
use Authentication\Domain\Model\Identity\CredentialId;
use Authentication\Domain\ValueObject\Token\MagicLinkToken;
use Identity\Domain\ValueObject\Identifier;
use Kernel\Application\Clock\SystemTime;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Credential pour authentification par Magic Link
 */
final class MagicLinkCredential extends AbstractAccessCredential
{

    public static function create(
        Identifier $identifier,
        MagicLinkToken $token,
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

    public function attachToUser(UserId $userId): void
    {
        $this->userId = $userId;
    }

    public function getType(): CredentialType
    {
        return CredentialType::MAGIC_LINK;
    }

    public function isValid(): bool
    {
        return $this->usedAt === null && !$this->isExpired(new \DateTimeImmutable());
    }

}
