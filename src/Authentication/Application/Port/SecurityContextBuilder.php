<?php

namespace Authentication\Application\Port;

use Authentication\Infrastructure\Security\UserSecurity;
use SharedKernel\Domain\DTO\Account\AccountStatusDTO;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Construit le contexte de sécurité minimal pour Symfony.
 * Pas de logique de plan, juste l'essentiel.
 */
final class SecurityContextBuilder
{
    /**
     * Construit l'objet UserSecurity pour Symfony.
     */
    public function buildSecurityUser(
        UserId $userId,
        string $identifier,
        AccountStatusDTO $accountStatus
    ): UserSecurity {
        // Rôles basiques selon le statut
        $roles = $this->determineBasicRoles($accountStatus);

        return new UserSecurity(
            userId: $userId,
            identifier: $identifier,
            roles: $roles
        );
    }

    /**
     * Détermine les rôles minimaux selon le statut.
     */
    private function determineBasicRoles(AccountStatusDTO $status): array
    {
        // Tout le monde a ROLE_USER
        $roles = ['ROLE_USER'];

        // Si le compte n'est pas actif
        if (!$status->isActive()) {
            return ['ROLE_INACTIVE'];
        }

        return $roles;
    }
}
