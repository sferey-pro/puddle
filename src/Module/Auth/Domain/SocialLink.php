<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain;

use App\Module\Auth\Domain\ValueObject\Social;
use App\Module\Auth\Domain\ValueObject\SocialLinkId;

/**
 * Représente l'association entre un compte utilisateur de notre application
 * et un compte sur un réseau social externe (ex: Google, GitHub).
 * Cet objet simple stocke quel compte externe est lié à quel compte interne.
 * C'est une entité enfant de l'agrégat UserAccount.
 */
final readonly class SocialLink
{

    private(set) \DateTimeImmutable $createdAt;
    private(set) \DateTimeImmutable $updatedAt;

    public function __construct(
        private SocialLinkId $id,
        private Social $social,
        private ?UserAccount $user = null,
        private ?bool $isActive = null,
    ) {
    }

    // --- Accesseurs ---
    public function id(): SocialLinkId
    {
        return $this->id;
    }

    public function social(): Social
    {
        return $this->social;
    }

    public function user(): ?UserAccount
    {
        return $this->user;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }
}
