<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain;

use App\Core\Domain\Aggregate\AggregateRoot;
use App\Core\Domain\Event\DomainEventTrait;
use App\Module\Auth\Domain\Event\AuthenticationDisabled;
use App\Module\Auth\Domain\Event\UserAccountCreated;
use App\Module\Auth\Domain\Event\UserAccountRolesSynced;
use App\Module\Auth\Domain\ValueObject\Identifier;
use App\Module\SharedContext\Domain\ValueObject\Roles;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Représente le compte d'un utilisateur dans le système d'authentification.
 * Cet objet est le cœur de la sécurité : il gère les informations d'identification
 * (email ou phone_number), les permissions (rôles) et le statut du compte (actif).
 *
 * Il est responsable de toutes les actions liées à la sécurité comme l'inscription,
 * la connexion, et protège le compte contre les tentatives de connexion frauduleuses.
 */
class UserAccount extends AggregateRoot
{
    use DomainEventTrait;

    private(set) bool $isEnabled = true;
    private(set) Roles $roles;

    private function __construct(
        private(set) UserId $id,
        private(set) Identifier $identifier,
    ) {
        $this->roles = Roles::user();
    }

    public static function create(UserId $userId, Identifier $identifier): self
    {
        $userAccount = new self($userId, $identifier);

        $userAccount->recordDomainEvent(
            new UserAccountCreated(
                $userAccount->id,
                $userAccount->identifier,
                $userAccount->roles
            )
        );

        return $userAccount;
    }

    public function syncRoles(Roles $newRoles): void
    {
        if ($this->roles->equals($newRoles)) {
            return;
        }

        $this->roles = $newRoles;

        $this->recordDomainEvent(
            new UserAccountRolesSynced($this->id, $this->roles)
        );
    }

    /**
     * Désactive la possibilité pour ce compte de s'authentifier.
     */
    public function disableAuthentication(string $reason): void
    {
        if (!$this->isEnabled) {
            return;
        }

        $this->isEnabled = false;
        $this->recordDomainEvent(new AuthenticationDisabled($this->id, $reason));
    }

}
