<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain;

use App\Core\Application\Clock\ClockInterface;
use App\Core\Domain\Aggregate\AggregateRoot;
use App\Core\Domain\Event\DomainEventTrait;
use App\Module\Auth\Domain\Enum\Role;
use App\Module\Auth\Domain\Event\LoginLinkGenerated;
use App\Module\Auth\Domain\Event\LoginLinkVerified;
use App\Module\Auth\Domain\Event\UserAccountAssociated;
use App\Module\Auth\Domain\Event\UserLoggedIn;
use App\Module\Auth\Domain\Event\UserLoggedOut;
use App\Module\Auth\Domain\Event\UserPasswordChanged;
use App\Module\Auth\Domain\Event\UserRegistered;
use App\Module\Auth\Domain\Event\UserVerified;
use App\Module\Auth\Domain\Exception\LoginLinkException;
use App\Module\Auth\Domain\Exception\PasswordResetException;
use App\Module\Auth\Domain\ValueObject\Hash;
use App\Module\Auth\Domain\ValueObject\IpAddress;
use App\Module\Auth\Domain\ValueObject\LoginLinkDetails;
use App\Module\Auth\Domain\ValueObject\Password;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\SharedContext\Domain\ValueObject\Username;
use App\Module\UserManagement\Domain\Event\UserAccountSuspended;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Représente le compte d'un utilisateur dans le système d'authentification.
 * Cet objet est le cœur de la sécurité : il gère les informations d'identification
 * (email, mot de passe), les permissions (rôles) et le statut du compte (actif, vérifié).
 * Il est responsable de toutes les actions liées à la sécurité comme l'inscription,
 * la connexion, et protège le compte contre les tentatives de connexion frauduleuses.
 *
 * @TODO Faire évoluer en readonly si c'est possible (à examiner)
 */
class UserAccount extends AggregateRoot implements UserInterface, PasswordAuthenticatedUserInterface
{
    use DomainEventTrait;

    // --- Propriétés liées à la sécurité du compte ---

    /**
     * @var int Compteur des tentatives de connexion échouées.
     *          Utilisé pour détecter et bloquer les attaques par force brute.
     */
    private int $loginFailureAttempts = 0;

    /**
     * @var \DateTimeImmutable|null Date de la dernière tentative de connexion échouée.
     *                              Permet de réinitialiser le compteur après un certain temps.
     */
    private ?\DateTimeImmutable $lastLoginFailureAt = null;

    /**
     * @var int nombre maximum de tentatives de connexion autorisées avant de suspendre le compte
     */
    private const MAX_LOGIN_ATTEMPTS = 3;

    /** @var Collection<int, LoginLink> Stocke les liens de connexion magiques ("magic links") générés pour l'utilisateur. */
    private Collection $loginLinks;

    /** @var Collection<int, SocialLink> Stocke les associations avec des comptes de réseaux sociaux (Google, GitHub, etc.). */
    private Collection $socialLinks;

    /**
     * Le constructeur est privé pour forcer la création d'un compte
     * via une méthode métier explicite (register, createAssociated).
     * Cela garantit que chaque compte est créé dans un état valide.
     */
    private function __construct(
        private UserId $id,
        private ?Email $email = null,
        private ?Username $username = null,
        #[\SensitiveParameter] private ?Password $password = null,
        private array $roles = [],
        private bool $isVerified = false,
        private bool $isActive = true,
    ) {
        $this->loginLinks = new ArrayCollection();
        $this->socialLinks = new ArrayCollection();
    }

    /**
     * Crée un compte utilisateur suite à une création depuis le gestionnaire d'utilisateur.
     * Le compte est initialement crée avec un mot de passe aléatoire non communiqué
     * à l'utilisateur et est considéré comme actif.
     *
     * @return self le nouveau compte associé créé
     */
    public static function createAssociated(UserId $id, Email $email, ?Username $username = null): self
    {
        $user = new self(
            $id,
            $email,
            $username,
            $password ?? Password::random(), // Un mot de passe est généré aléatoirement si aucun n'est fourni.
            [Role::USER], // Tout nouvel utilisateur obtient le rôle de base "USER".
            false, // Le compte n'est pas vérifié par défaut.
            true // Le compte est actif par défaut.
        );

        // Notifie le reste du système qu'un compte a été créé via une source externe (gestionnaire d'utilisateur).
        $user->recordDomainEvent(
            new UserAccountAssociated($user->id(), $user->email())
        );

        return $user;
    }

    /**
     * Crée un nouveau compte utilisateur suite à une inscription classique (email/mot de passe).
     * C'est le point d'entrée pour enregistrer un nouvel utilisateur.
     *
     * @return self le nouveau compte utilisateur créé
     */
    public static function register(UserId $id, Email $email, ?Username $username = null, #[\SensitiveParameter] ?Password $password = null): self
    {
        $user = new self(
            $id,
            $email,
            $username,
            $password,
            [Role::USER], // Tout nouvel utilisateur obtient le rôle de base "USER".
            false, // Le compte n'est pas vérifié par défaut.
            true // Le compte est actif par défaut.
        );

        // Notifie le système qu'un nouvel utilisateur s'est inscrit (pour envoyer un email de bienvenue, etc.).
        $user->recordDomainEvent(
            new UserRegistered($user->id(), $user->email())
        );

        return $user;
    }

    /**
     * Enregistre une connexion réussie. Peut être utilisé pour l'historique ou des actions post-connexion.
     */
    public static function login(UserId $id, Email $email): self
    {
        $user = new self($id, $email);

        $user->recordDomainEvent(
            new UserLoggedIn($user->id())
        );

        return $user;
    }

    /**
     * Enregistre une déconnexion.
     */
    public static function logout(UserId $id): self
    {
        $user = new self($id);
        $user->recordDomainEvent(
            new UserLoggedOut($user->id())
        );

        return $user;
    }

    /**
     * Met à jour le mot de passe du compte.
     */
    public function changePassword(#[\SensitiveParameter] Password $password): void
    {
        $this->password = $password;

        $this->recordDomainEvent(
            new UserPasswordChanged($this->id())
        );
    }

    /**
     * Réinitialise le mot de passe de l'utilisateur après vérification du token.
     *
     * @throws PasswordResetException si la demande est invalide, expirée ou déjà utilisée
     */
    public function resetPassword(
        PasswordResetRequest $request,
        Password $newPassword,
        \DateTimeImmutable $now,
    ): void {
        if (!$this->id->equals($request->userId())) {
            throw PasswordResetException::userMismatch($request->userId(), $this->id());
        }

        if ($request->isExpired($now)) {
            throw PasswordResetException::expired();
        }

        if ($request->isUsed()) {
            throw PasswordResetException::alreadyUsed();
        }

        $this->changePassword($newPassword);

        $request->markAsUsed();
    }

    /**
     * Marque le compte comme vérifié (ex: après un clic sur un lien dans un e-mail).
     */
    public function verified(): void
    {
        $this->isVerified = true;

        $this->recordDomainEvent(
            new UserVerified($this->id())
        );
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
     * Enregistre une tentative de connexion échouée.
     * Après un certain nombre d'échecs, le compte est suspendu pour sécurité.
     */
    public function recordLoginFailure(ClockInterface $clock): void
    {
        if (!$this->isActive()) {
            return;
        }

        // Si la dernière tentative est suffisamment ancienne, on réinitialise le compteur.
        if ($this->lastLoginFailureAt && $this->lastLoginFailureAt->diff($clock->now())->i >= 15) {
            $this->loginFailureAttempts = 0;
        }

        ++$this->loginFailureAttempts;
        $this->lastLoginFailureAt = $clock->now();

        if ($this->loginFailureAttempts >= self::MAX_LOGIN_ATTEMPTS) {
            $this->suspend();
        }
    }

    /**
     * Suspend le compte, empêchant toute nouvelle connexion.
     * Déclenche un événement pour notifier (par exemple, envoyer un e-mail d'alerte).
     */
    public function suspend(): void
    {
        if (!$this->isActive()) {
            return;
        }

        $this->isActive = false;

        $this->recordDomainEvent(
            new UserAccountSuspended($this->id(), 'Too many login failures')
        );
    }

    // --- Accesseurs ---
    public function id(): UserId
    {
        return $this->id;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function username(): ?Username
    {
        return $this->username;
    }

    public function password(): ?Password
    {
        return $this->password;
    }

    public function enumRoles(): array
    {
        return array_map(fn (string $role): ?Role => Role::tryFrom($role), $this->roles());
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function roles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    // --- @TODO trouver une nouvelle solution ---
    public function setHashPassword(Password $password): void
    {
        $this->password = $password;
    }

    // --- Méthodes requises par Symfony Security ---
    // --- @TODO à retirer du Domain ---

    /** @see PasswordAuthenticatedUserInterface */
    public function getPassword(): ?string
    {
        return $this->password()->value;
    }

    /** @see UserInterface */
    public function getRoles(): array
    {
        return $this->roles();
    }

    /** @TODO à faire évoluer pour qu'il retourne le role le plus important parmis ce présent dans le tableau */
    public function getMainRole(): Role
    {
        return Role::USER;
    }

    /** @see UserInterface */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /** @see UserInterface */
    public function getUserIdentifier(): string
    {
        return (string) $this->email();
    }
}
