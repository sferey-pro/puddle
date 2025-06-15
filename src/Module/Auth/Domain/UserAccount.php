<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain;

use App\Module\Auth\Domain\Enum\Role;
use App\Module\Auth\Domain\Event\UserLoggedIn;
use App\Module\Auth\Domain\Event\UserLoggedOut;
use App\Module\Auth\Domain\Event\UserPasswordChanged;
use App\Module\Auth\Domain\Event\UserRegistered;
use App\Module\Auth\Domain\Event\UserVerified;
use App\Module\Auth\Domain\ValueObject\Password;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Model\DomainEventTrait;
use SensitiveParameter;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserAccount extends AggregateRoot implements UserInterface, PasswordAuthenticatedUserInterface
{
    use DomainEventTrait;

    private function __construct(
        private UserId $id,
        private ?Email $email = null,
        #[SensitiveParameter] private ?Password $password = null,
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
     * @param UserId        $id       L'identifiant unique de l'utilisateur
     * @param Email         $email    L'adresse e-mail de l'utilisateur
     * @param Password|null $password le mot de passe de l'utilisateur (optionnel, sera généré si null)
     *
     * @return self la nouvelle instance de UserAccount
     */
    public static function register(
        UserId $id,
        Email $email,
        #[SensitiveParameter] ?Password $password = null,
    ): self {
        $user = new self(
            id: $id,
            password: $password ?? new Password(md5(random_bytes(10))),
            email: $email,
            roles: [Role::USER],
            isVerified: false
        );

        $user->recordDomainEvent(
            new UserRegistered($user->id(), $user->email())
        );

        return $user;
    }

    /**
     * Enregistre le fait que l'utilisateur s'est connecté.
     * Cet événement est généralement déclenché après une authentification réussie.
     */
    public static function login(
        UserId $id,
        Email $email,
    ): self {
        $user = new self($id, $email);

        $user->recordDomainEvent(new UserLoggedIn($user->id()));

        return $user;
    }

    /**
     * Enregistre le fait que l'utilisateur s'est déconnecté.
     */
    public static function logout(UserId $id): self
    {
        $user = new self($id);
        $user->recordDomainEvent(new UserLoggedOut($user->id()));

        return $user;
    }

    /**
     * Modifie le mot de passe de l'utilisateur.
     * Enregistre un événement UserPasswordChanged.
     *
     * @param Password $password le nouveau mot de passe de l'utilisateur
     */
    public function changePassword(#[SensitiveParameter] Password $password): void
    {
        $this->password = $password;

        $this->recordDomainEvent(new UserPasswordChanged(id: $this->id));
    }

    /**
     * Marque le compte de l'utilisateur comme vérifié.
     *
     * Enregistre un événement UserVerified.
     */
    public function verified(): void
    {
        $this->isVerified = true;

        $this->recordDomainEvent(new UserVerified($this->id, true));
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function password(): ?Password
    {
        return $this->password;
    }

    /**
     * A visual id that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email();
    }

    /**
     * @return list<Role|null>
     */
    public function getEnumRoles(): array
    {
        return array_map(fn (string $role): ?Role => Role::tryFrom($role), $this->roles());
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
    public function roles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function getRoles(): array
    {
        return $this->roles();
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password()->value;
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
