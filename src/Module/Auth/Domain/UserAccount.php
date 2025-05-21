<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain;

use App\Module\Auth\Domain\Event\UserLoggedIn;

use App\Module\Auth\Domain\Enum\Role;
use App\Module\Auth\Domain\Event\UserLoggedOut;
use App\Module\Auth\Domain\Event\UserPasswordChanged;
use App\Module\Auth\Domain\Event\UserRegistered;
use App\Module\Auth\Domain\Event\UserVerified;
use App\Module\Auth\Domain\ValueObject\Password;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Model\DomainEventTrait;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserAccount extends AggregateRoot implements UserInterface, PasswordAuthenticatedUserInterface
{
    use DomainEventTrait;

    private function __construct(
        private UserId $identifier,
        private ?Password $password = null,
        private ?Email $email = null,
        private array $roles = [],
        private bool $isVerified = false,
    ) {
    }

    /**
     * Crée une nouvelle instance de UserAccount pour un nouvel utilisateur.
     *
     * Initialise l'utilisateur avec un rôle par défaut et un statut non vérifié.
     * Un mot de passe aléatoire est généré si aucun n'est fourni.
     * Enregistre un événement UserRegistered.
     *
     * @param UserId $identifier L'identifiant unique de l'utilisateur.
     * @param Email $email L'adresse e-mail de l'utilisateur.
     * @param Password|null $password Le mot de passe de l'utilisateur (optionnel, sera généré si null).
     * @return self La nouvelle instance de UserAccount.
     */
    public static function register(
        UserId $identifier,
        Email $email,
        ?Password $password = null,
    ): self {
        $user = new self(
            identifier: $identifier,
            password: $password ?? new Password(md5(random_bytes(10))),
            email: $email,
            roles: [Role::USER],
            isVerified: false
        );

        $user->recordDomainEvent(
            new UserRegistered(identifier: $user->identifier(), email: $user->email())
        );

        return $user;
    }

    /**
     * Enregistre le fait que l'utilisateur s'est connecté.
     * Cet événement est généralement déclenché après une authentification réussie.
     */
    public static function login(UserId $identifier): self
    {
        $user = new self($identifier);
        $user->recordDomainEvent(new UserLoggedIn(identifier: $user->identifier()));

        return $user;
    }

    /**
     * Enregistre le fait que l'utilisateur s'est déconnecté.
     */
    public static function logout(UserId $identifier): self
    {
        $user = new self($identifier);
        $user->recordDomainEvent(new UserLoggedOut(identifier: $user->identifier()));

        return $user;
    }

    /**
     * Modifie le mot de passe de l'utilisateur.
     * Enregistre un événement UserPasswordChanged.
     *
     * @param Password $password Le nouveau mot de passe de l'utilisateur.
     */
    public function changePassword(Password $password): void
    {
        $this->password = $password;

        $this->recordDomainEvent(new UserPasswordChanged(identifier: $this->identifier));
    }

    /**
     * Marque le compte de l'utilisateur comme vérifié.
     *
     * Enregistre un événement UserVerified.
     */
    public function verified(): void
    {
        $this->isVerified = true;

        $this->recordDomainEvent(new UserVerified(identifier: $this->identifier));
    }

    public function identifier(): UserId
    {
        return $this->identifier;
    }

    public function email(): Email
    {
        return $this->email;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @return list<Role|null>
     */
    public function getEnumRoles(): array
    {
        return array_map(fn (string $role): ?Role => Role::tryFrom($role), $this->getRoles());
    }

    public function getMainRole(): Role
    {
        return Role::USER;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password->value;
    }

    public function setHashPassword(Password $password): void
    {
        $this->password = $password;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }


}
