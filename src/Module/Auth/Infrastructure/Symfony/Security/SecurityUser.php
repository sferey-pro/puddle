<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Symfony\Security;

use App\Module\Auth\Domain\ValueObject\Identifier;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class SecurityUser.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SecurityUser implements UserInterface, PasswordAuthenticatedUserInterface, EquatableInterface
{
    /**
     * @var int nombre maximum de tentatives de connexion autorisées avant de suspendre le compte
     */
    private const MAX_LOGIN_ATTEMPTS = 3;

    private function __construct(
        private(set) UserId $id,
        private(set) Identifier $identifier,
        private(set) ?string $password,
        private(set) array $roles,
    ) {

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
        $identifier = $this->identifier->value;

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
