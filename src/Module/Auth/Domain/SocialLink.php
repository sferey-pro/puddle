<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain;

use App\Module\Auth\Domain\AuthenticationMethod;
use App\Module\Auth\Domain\Exception\SocialLinkException;
use App\Module\Auth\Domain\ValueObject\Social;
use App\Module\Auth\Domain\ValueObject\SocialLinkId;

/**
 * Représente l'association entre un compte utilisateur de notre application
 * et un compte sur un réseau social externe (ex: Google, GitHub).
 * Cet objet simple stocke quel compte externe est lié à quel compte interne.
 * C'est une entité enfant de l'agrégat UserAccount.
 */
final readonly class SocialLink implements AuthenticationMethod
{
    private(set) SocialLinkId $id;
    private(set) Social $social;

    private(set) UserAccount $user;
    private(set) bool $isActive;

    private(set) \DateTimeImmutable $createdAt;
    private(set) \DateTimeImmutable $updatedAt;

    private function __construct() {

    }

    public static function create(
        UserAccount $user,
        Social $social,
    ): self {
        $socialLink = new self();
        $socialLink->id = SocialLinkId::generate();

        $socialLink->user = $user;
        $socialLink->social = $social;

        $socialLink->isActive = false;

        return $socialLink;
    }

    public function activate(): self
    {
        if ($this->isActive) {
            throw SocialLinkException::alreadyActivated();
        }


        $socialLink = clone $this;
        $socialLink->activated();

        return $socialLink;
    }

    public function activated(): static
    {
        $this->isActive = true;

        return $this;
    }
}
