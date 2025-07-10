<?php

declare(strict_types=1);

// src/Account/Lifecycle/Domain/State/DeletedState.php

namespace Account\Lifecycle\Domain\State;

use Account\Core\Domain\Account;

/**
 * Représente l'état terminal d'un compte supprimé.
 *
 * Aucune transition n'est possible depuis cet état. Toute tentative
 * de modifier un compte supprimé doit lever une exception.
 */
final class DeletedState implements AccountState
{
    public function getName(): string
    {
        return 'deleted';
    }

    public function canBeVerified(Account $account): bool
    {
        return false;
    }

    public function verify(Account $account): void
    {
        throw new \DomainException('Cannot perform any action on a deleted account.');
    }

    public function canBeSuspended(Account $account): bool
    {
        return false;
    }

    public function suspend(Account $account, string $reason): void
    {
        throw new \DomainException('Cannot perform any action on a deleted account.');
    }

    public function canBeReactivated(Account $account): bool
    {
        return false;
    }

    public function reactivate(Account $account): void
    {
        throw new \DomainException('Cannot perform any action on a deleted account.');
    }

    public function canBeLocked(Account $account): bool
    {
        return false;
    }

    public function lock(Account $account, string $reason): void
    {
        throw new \DomainException('Cannot perform any action on a deleted account.');
    }

    public function canBeDeleted(Account $account): bool
    {
        return false; // Déjà supprimé
    }

    public function delete(Account $account): void
    {
        throw new \DomainException('Account is already deleted.');
    }

    /**
     * Retourne un tableau vide car aucune transition n'est possible.
     */
    public function getPossibleTransitions(): array
    {
        return [];
    }
}
