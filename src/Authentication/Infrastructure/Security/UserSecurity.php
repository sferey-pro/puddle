<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Security;

use Identity\Domain\ValueObject\Identifier;
use SharedKernel\Domain\ValueObject\Identity\UserId;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Data Transfer Object pour Symfony Security.
 *
 * ⚠️ ATTENTION : Ce n'est PAS un service !
 * Cette classe est instanciée dynamiquement par UserProvider/SecurityContextBuilder.
 *
 * @see UserProvider::loadUserByIdentifier()
 * @see SecurityContextBuilder::buildSecurityUser()
 *
 * @internal Ne pas injecter cette classe comme dépendance
 */
#[Exclude]
final readonly class UserSecurity implements UserInterface
{
    public function __construct(
        private(set) UserId $userId,
        private(set) Identifier $identifier,
        private array $roles = ['ROLE_USER']
    ) {}

    /** Le ContractAwareSubscriber mettra à jour createdBy et updatedBy automatiquement */
    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void { }

    public function getUserIdentifier(): string
    {
        return $this->identifier->value();
    }
}
