<?php

declare(strict_types=1);

namespace Authentication\Domain\Model\AccessCredential;

use Authentication\Domain\Enum\CredentialType;
use Authentication\Domain\Model\Identity\CredentialId;
use Authentication\Domain\ValueObject\Token;
use Identity\Domain\ValueObject\Identifier;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Classe abstraite pour tous les types de credentials.
 * Utilise SINGLE_TABLE inheritance avec Doctrine.
 */
abstract class AbstractAccessCredential
{
    protected(set) ?UserId $userId;
    protected(set) ?\DateTimeInterface $usedAt;
    protected array $metadata = [];

    protected function __construct(
        private(set) CredentialId $id,
        private(set) Identifier $identifier,
        private(set) Token $token,
        private(set) \DateTimeInterface $createdAt,
        private(set) \DateTimeInterface $expiresAt
    ) {}

    /**
     * Type de credential (discriminator).
     */
    abstract public function getType(): CredentialType;

    /**
     * Ajoute des métadonnées.
     */
    public function addMetadata(string $key, mixed $value): void
    {
        $this->metadata[$key] = $value;
    }

    public function isExpired(\DateTimeInterface $now): bool
    {
        return $this->token->isExpired(\DateTimeImmutable::createFromInterface($now));
    }

    public function markAsUsed(\DateTimeInterface $when): void
    {
        if ($this->usedAt !== null) {
            throw new \DomainException('Already used');
        }
        $this->usedAt = $when;
    }
}
