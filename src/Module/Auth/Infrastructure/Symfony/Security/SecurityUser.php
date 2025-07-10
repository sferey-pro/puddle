<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Symfony\Security;

use App\Module\Auth\Domain\ValueObject\UserIdentity;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class SecurityUser implements UserInterface, PasswordAuthenticatedUserInterface, EquatableInterface
{
    private(set) UserId $id;
    private(set) UserIdentity $identity;
    private(set) ?string $password;
    private(set) array $roles;
    public bool $isLocked;

    private function __construct() { }

    public static function create(UserIdentity $identity, ?string $password = null, array $roles = []): self
    {
        $user = new self();
        $user->id = UserId::generate();
        $user->identity = $identity;
        $user->password = $password;
        $user->roles = $roles;
        $user->isLocked = false;

        return $user;
    }

    #[\Override]
    public function getRoles(): array
    {
        return $this->roles;
    }

    #[\Override]
    public function eraseCredentials(): void { }

    #[\Override]
    public function getUserIdentifier(): string
    {
        /** @var non-empty-string $identifier */
        $identifier = $this->identity->value;

        return $identifier;
    }

    #[\Override]
    public function getPassword(): ?string
    {
        return $this->password;
    }

    #[\Override]
    public function isEqualTo(UserInterface $user): bool
    {
        if (! $user instanceof self) {
            return false;
        }

        return $this->id->equals($user->id);
    }
}
