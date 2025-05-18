<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Model;

use App\Module\Auth\Domain\Enum\Role;
use App\Module\Auth\Domain\Event\UserRegistered;
use App\Module\Auth\Domain\ValueObject\Password;
use App\Module\Shared\Domain\ValueObject\Email;
use App\Module\Shared\Domain\ValueObject\UserId;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Model\DomainEventTrait;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User extends AggregateRoot implements UserInterface, PasswordAuthenticatedUserInterface
{
    use DomainEventTrait;

    private function __construct(
        private UserId $identifier,
        private Password $password,
        private Email $email,
        private array $roles = [],
        private bool $isVerified = false,
    ) {
    }

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

    public function identifier(): UserId
    {
        return $this->identifier;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function changePassword(Password $password): void
    {
        $this->password = $password;
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

    public function verified(): static
    {
        $this->isVerified = true;

        return $this;
    }
}
