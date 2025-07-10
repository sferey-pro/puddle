<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain;

use App\Core\Application\Clock\ClockInterface;
use App\Core\Domain\Aggregate\AggregateRoot;
use App\Core\Domain\Event\DomainEventTrait;
use App\Module\Auth\Domain\Enum\Role;
use App\Module\Auth\Domain\Event\LoginLinkGenerated;
use App\Module\Auth\Domain\Event\LoginLinkVerified;
use App\Module\Auth\Domain\Event\UserAccountCreated;
use App\Module\Auth\Domain\Event\UserLoggedIn;
use App\Module\Auth\Domain\Event\UserLoggedOut;
use App\Module\Auth\Domain\Event\UserPasswordChanged;
use App\Module\Auth\Domain\Event\UserVerified;
use App\Module\Auth\Domain\Exception\LoginLinkException;
use App\Module\Auth\Domain\Exception\PasswordResetException;
use App\Module\Auth\Domain\ValueObject\EmailIdentity;
use App\Module\Auth\Domain\ValueObject\Hash;
use App\Module\Auth\Domain\ValueObject\IpAddress;
use App\Module\Auth\Domain\ValueObject\LoginLinkDetails;
use App\Module\Auth\Domain\ValueObject\Password;
use App\Module\Auth\Domain\ValueObject\PhoneIdentity;
use App\Module\Auth\Domain\ValueObject\UserIdentity;
use App\Module\SharedContext\Domain\ValueObject\EmailAddress;
use App\Module\SharedContext\Domain\ValueObject\PhoneNumber;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Domain\Event\UserAccountSuspended;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Représente le compte d'un utilisateur dans le système d'authentification.
 * Cet objet est le cœur de la sécurité : il gère les informations d'identification
 * (EmailAddress, mot de passe), les permissions (rôles) et le statut du compte (actif, vérifié).
 * Il est responsable de toutes les actions liées à la sécurité comme l'inscription,
 * la connexion, et protège le compte contre les tentatives de connexion frauduleuses.
 *
 */
final class UserAccount extends AggregateRoot
{
    use DomainEventTrait;

    private(set) UserId $id;
    private(set) array $roles;
    private(set) bool $verified;
    private(set) bool $active;
    private(set) ?EmailAddress $email = null;
    private(set) ?PhoneNumber $phone = null;

    private(set) \DateTimeImmutable $createdAt;
    private(set) \DateTimeImmutable $updatedAt;

    private function __construct() {}

    public static function create(UserId $id, UserIdentity $identity): self
    {
        $user = new self();
        $user->id = $id;
        $user->roles = [Role::USER];
        $user->verified = false;
        $user->active = true;

        match ($identity::class) {
            EmailIdentity::class => $user->email = $identity->email,
            PhoneIdentity::class => $user->phone = $identity->phone,
        };

        $user->recordDomainEvent(
            new UserAccountCreated($user->id, $user->email, $user->phone)
        );

        return $user;
    }

    /**
     * Enregistre une connexion réussie. Peut être utilisé pour l'historique ou des actions post-connexion.
     */
    public static function login(UserId $id, UserIdentity $identity): self
    {
        $user = new self();
        $user->id = $id;

        match ($identity::class) {
            EmailIdentity::class => $user->email = $identity->email,
            PhoneIdentity::class => $user->phone = $identity->phone,
        };

        $user->recordDomainEvent(
            new UserLoggedIn($user->id, $user->email, $user->phone)
        );

        return $user;
    }

    /**
     * Enregistre une déconnexion.
     */
    public static function logout(UserId $id): self
    {
        $user = new self();
        $user->id = $id;

        $user->recordDomainEvent(
            new UserLoggedOut($user->id)
        );

        return $user;
    }

    /**
     * Marque le compte comme vérifié (ex: après un clic sur un lien dans un e-mail).
     */
    public function verified(): void
    {
        $this->verified = true;

        $this->recordDomainEvent(
            new UserVerified($this->id)
        );
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

        $this->active = false;

        $this->recordDomainEvent(
            new UserAccountSuspended($this->id(), 'Too many login failures')
        );
    }

    // --- Accesseurs ---
    public function enumRoles(): array
    {
        return array_map(fn (string $role): ?Role => Role::tryFrom($role), $this->roles());
    }

    public function isVerified(): bool
    {
        return $this->verified;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function roles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
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
}
