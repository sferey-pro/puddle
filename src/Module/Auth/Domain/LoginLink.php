<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain;

use App\Module\Auth\Domain\Exception\LoginLinkException;
use App\Module\Auth\Domain\ValueObject\IpAddress;
use App\Module\Auth\Domain\ValueObject\LoginLinkDetails;
use App\Module\Auth\Domain\ValueObject\LoginLinkId;
use App\Shared\Domain\Service\ClockInterface;

/**
 * Représente un lien de connexion magique ("magic link").
 * Cet objet encapsule toutes les informations et règles liées à un lien de connexion
 * à usage unique : à quel utilisateur il appartient, sa date d'expiration,
 * s'il a déjà été utilisé, et depuis quelle adresse IP il a été demandé.
 * C'est une entité enfant de l'agrégat UserAccount.
 */
readonly class LoginLink
{
    /**
     * Le constructeur est privé pour s'assurer que sa création passe
     * par une méthode métier explicite comme `createFor()`.
     */
    public function __construct(
        private LoginLinkId $id,
        private UserAccount $user,
        private LoginLinkDetails $details,
        private IpAddress $ipAddress,
        private bool $isVerified = false,
    ) {
    }

    /**
     * Crée un nouveau lien de connexion pour un utilisateur donné.
     *
     * @param UserAccount      $user      le compte utilisateur pour lequel le lien est créé
     * @param LoginLinkDetails $details   les détails du lien (le code unique, la date d'expiration)
     * @param IpAddress        $ipAddress L'adresse IP de l'appareil ayant demandé le lien, pour la sécurité
     *
     * @return self le nouvel objet lien de connexion
     */
    public static function createFor(
        UserAccount $user,
        LoginLinkDetails $details,
        IpAddress $ipAddress,
    ): self {
        return new self(
            id: LoginLinkId::generate(),
            user: $user,
            details: $details,
            ipAddress: $ipAddress,
            // isVerified est false par défaut, car le lien vient juste d'être créé.
        );
    }

    /**
     * Marque ce lien comme "vérifié", c'est-à-dire utilisé avec succès.
     * Avant de le valider, on vérifie qu'il n'a pas déjà été utilisé et qu'il n'a pas expiré.
     *
     * @param ClockInterface $clock le service d'heure pour vérifier l'expiration
     *
     * @throws LoginLinkException si le lien est déjà vérifié ou a expiré
     *
     * @return self une nouvelle instance du lien marqué comme vérifié
     */
    public function markAsVerified(ClockInterface $clock): self
    {
        if ($this->isVerified) {
            throw LoginLinkException::alreadyVerified();
        }

        if ($this->isExpired($clock)) {
            throw LoginLinkException::expired();
        }

        // On crée une copie de l'objet avec le statut vérifié.
        $verifiedLogin = new self(
            $this->id(),
            $this->user(),
            $this->details(),
            $this->ipAddress(),
            isVerified: true,
        );

        return $verifiedLogin;
    }

    /**
     * Vérifie si le lien a expiré en comparant sa date d'expiration avec l'heure actuelle.
     */
    public function isExpired(ClockInterface $clock): bool
    {
        return $this->details()->expiresAt < $clock->now();
    }

    // --- Accesseurs ---
    public function id(): LoginLinkId
    {
        return $this->id;
    }

    public function user(): ?UserAccount
    {
        return $this->user;
    }

    public function details(): ?LoginLinkDetails
    {
        return $this->details;
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function ipAddress(): ?IpAddress
    {
        return $this->ipAddress;
    }

    public function verified(): static
    {
        $this->isVerified = true;

        return $this;
    }
}
