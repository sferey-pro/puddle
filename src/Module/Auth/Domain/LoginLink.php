<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain;

use App\Module\Auth\Domain\AuthenticationMethod;
use App\Core\Application\Clock\ClockInterface;
use App\Module\Auth\Domain\Exception\LoginLinkException;
use App\Module\Auth\Domain\ValueObject\IpAddress;
use App\Module\Auth\Domain\ValueObject\LoginLinkDetails;
use App\Module\Auth\Domain\ValueObject\LoginLinkId;

/**
 * Représente un lien de connexion magique ("magic link").
 * Cet objet encapsule toutes les informations et règles liées à un lien de connexion
 * à usage unique : à quel utilisateur il appartient, sa date d'expiration,
 * s'il a déjà été utilisé, et depuis quelle adresse IP il a été demandé.
 * C'est une entité enfant de l'agrégat UserAccount.
 */
final readonly class LoginLink implements AuthenticationMethod
{
    private(set) LoginLinkId $id;
    private(set) LoginLinkDetails $details;

    private(set) UserAccount $user;
    private(set) bool $isVerified;

    private(set) ?IpAddress $ipAddress;

    private(set) \DateTimeImmutable $createdAt;
    private(set) \DateTimeImmutable $updatedAt;

    private function __construct() {

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
    public static function create(
        UserAccount $user,
        LoginLinkDetails $details,
        ?IpAddress $ipAddress = null,
    ): self {
        $loginLink = new self();
        $loginLink->id = LoginLinkId::generate();

        $loginLink->user = $user;
        $loginLink->details = $details;
        $loginLink->ipAddress = $ipAddress;
        $loginLink->isVerified = false;

        return $loginLink;
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
    public function verify(ClockInterface $clock): self
    {
        if ($this->isVerified) {
            throw LoginLinkException::alreadyVerified();
        }

        if ($this->isExpired($clock)) {
            throw LoginLinkException::expired();
        }

        $verifiedLogin = clone $this;
        $verifiedLogin->verified();
        $verifiedLogin->updatedAt = $clock->now();

        return $verifiedLogin;
    }


    /**
     * Ajoute un "magic link" (lien de connexion sans mot de passe) pour cet utilisateur.
     */
    public function addLoginLink(
        LoginLinkDetails $loginLinkDetails,
        IpAddress $ipAddress,
    ): void {
        $login = LoginLink::createFor($this, $loginLinkDetails, $ipAddress);

        $this->loginLinks->add($login);

        // Notifie qu'un lien a été généré, par exemple pour l'envoyer par e-mail.
        $this->recordDomainEvent(
            new LoginLinkGenerated($this->id(), $this->email(), $loginLinkDetails)
        );
    }

    /**
     * Valide un "magic link" fourni par l'utilisateur.
     * Si le lien est correct et non expiré, il est marqué comme vérifié.
     */
    public function verifyLoginLink(Hash $hash, ClockInterface $clock): void
    {
        $loginLinkToVerify = null;

        $matchingLinks = $this->loginLinks->filter(
            fn (LoginLink $loginLink) => $loginLink->details()->hash->equals($hash)
        );

        if ($matchingLinks->isEmpty()) {
            throw LoginLinkException::notFoundWithHash($hash);
        }

        /** @var LoginLink $loginLinkToVerify */
        $loginLinkToVerify = $matchingLinks->first();

        $verifiedLogin = $loginLinkToVerify->markAsVerified($clock);

        $this->recordDomainEvent(
            new LoginLinkVerified($this->id(), $verifiedLogin->id())
        );

        $this->clearUnusedLoginLinks($verifiedLogin);
    }

    /**
     * Nettoie les anciens liens magiques après qu'un a été utilisé avec succès.
     */
    private function clearUnusedLoginLinks(LoginLink $justVerifiedLink): void
    {
        $linksToRemove = $this->loginLinks->filter(
            fn (LoginLink $link) => !$link->id()->equals($justVerifiedLink->id())
        );

        foreach ($linksToRemove as $link) {
            $this->loginLinks->removeElement($link);
        }
    }

    /**
     * Vérifie si le lien a expiré en comparant sa date d'expiration avec l'heure actuelle.
     */
    public function isExpired(ClockInterface $clock): bool
    {
        return $this->details->expiresAt < $clock->now();
    }

    public function verified(): static
    {
        $this->isVerified = true;

        return $this;
    }
}
