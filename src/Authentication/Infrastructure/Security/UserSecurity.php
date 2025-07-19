<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Security;

use Authentication\Domain\User;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class UserSecurity implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(
        private(set) User $user
    ) {
    }

    /** Le ContractAwareSubscriber mettra à jour createdBy et updatedBy automatiquement */
    public function getId(): string
    {
        return $this->user->id;
    }

    public function getRoles(): array { }

    public function eraseCredentials(): void { }

    public function getUserIdentifier(): string { }

    public function getPassword(): ?string { }

}
