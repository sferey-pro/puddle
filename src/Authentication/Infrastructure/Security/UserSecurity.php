<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Security;

use Authentication\Domain\User;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserSecurity implements UserInterface
{
    public function __construct(
        private readonly User $user
    ) {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return 'admin@gmail.com';
    }
}
