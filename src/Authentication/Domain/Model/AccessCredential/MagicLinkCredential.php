<?php

declare(strict_types=1);

namespace Authentication\Domain\Model\AccessCredential;

use Authentication\Domain\Enum\CredentialType;
use Authentication\Domain\Model\AccessCredential;
use Authentication\Domain\Model\Identity\CredentialId;
use Authentication\Domain\ValueObject\Token\MagicLinkToken;
use Identity\Domain\Model\ValueObject\Identifier;
use Kernel\Application\Clock\SystemTime;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Credential pour authentification par Magic Link
 */
final readonly class MagicLinkCredential implements AccessCredential
{
    private(set) ?UserId $userId;
    private ?\DateTimeInterface $usedAt;

    private function __construct(
        private(set) CredentialId $id,
        private(set) Identifier $identifier,
        private(set) MagicLinkToken $token,
        private(set) \DateTimeInterface $createdAt,
        private(set) \DateTimeInterface $expiresAt
    ) {}

    public static function create(
        Identifier $identifier,
        MagicLinkToken $token,
        ?\DateTimeImmutable $expiresAt = null
    ): self {
        $now = SystemTime::now();

        return new self(
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
    
    public function isExpired(\DateTimeInterface $now): bool
    {
        return $this->token->isExpired(\DateTimeImmutable::createFromInterface($now));
    }

    public function markAsUsed(\DateTimeInterface $when): void
    {
        if ($this->usedAt !== null) {
            throw new \DomainException('Magic link already used');
        }
        $this->usedAt = $when;
    }
}
