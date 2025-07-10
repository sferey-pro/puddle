<?php

declare(strict_types=1);

namespace Identity\Domain;

use Identity\Domain\ValueObject\AttachedIdentifierId;
use Identity\Domain\ValueObject\Identifier;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Entité interne à l'agrégat UserIdentity.
 * Elle n'est JAMAIS manipulée directement en dehors de l'agrégat.
 */
final readonly class AttachedIdentifier
{
    private function __construct(
        private AttachedIdentifierId $id,
        private(set) Identifier $identifier,
        private(set) bool $isPrimary,
        private(set) bool $isVerified,
        private(set) \DateTimeImmutable $attachedAt,
        private ?UserIdentity $userIdentity = null,
    ) {
    }

    /**
     * Factory method pour créer un AttachedIdentifier à partir d'un Identifier.
     */
    public static function fromIdentifier(Identifier $identifier, bool $isPrimary): self
    {
        return new self(
            AttachedIdentifierId::generate(),
            $identifier,
            $isPrimary,
            false,
            new \DateTimeImmutable()
        );
    }

    /**
     * Marque cet identifiant comme vérifié.
     */
    public function markAsVerified(): void
    {
        $this->isVerified = true;
    }

    public function markAsPrimary(): void
    {
        $this->isPrimary = true;
    }


    /**
     * Getter pour Doctrine uniquement.
     */
    public function getId(): AttachedIdentifierId
    {
        return $this->id;
    }
}
