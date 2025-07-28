<?php

declare(strict_types=1);

namespace Account\Lifecycle\Domain\Model\State;

use Account\Core\Domain\Model\Account;
use Account\Lifecycle\Domain\Model\AccountState;
use DomainException;

/**
 * Représente l'état d'un compte verrouillé.
 *
 * Le verrouillage est une mesure de sécurité forte. Un compte dans cet état
 * a des actions très limitées, nécessitant souvent une intervention manuelle
 * pour être réactivé.
 */
final readonly class LockedState implements AccountState
{
    /**
     * @param string $reason La raison du verrouillage.
     */
    public function __construct(
        private(set) string $reason
    ) {
    }

    public function getName(): string
    {
        return 'locked';
    }

    public function canBeVerified(Account $account): bool
    {
        return false;
    }

    public function verify(Account $account): void
    {
        throw new DomainException('Cannot verify a locked account.');
    }

    public function canBeSuspended(Account $account): bool
    {
        return false; // Le verrouillage est un état plus restrictif que la suspension.
    }

    public function suspend(Account $account, string $reason): void
    {
        throw new DomainException('Cannot suspend a locked account; it is a more restrictive state.');
    }

    public function canBeReactivated(Account $account): bool
    {
        return true; // La réactivation est la seule porte de sortie (hors suppression).
    }

    public function reactivate(Account $account): void
    {
        $account->changeState(new ActiveState());
    }

    public function canBeLocked(Account $account): bool
    {
        return false; // Déjà verrouillé.
    }

    public function lock(Account $account, string $reason): void
    {
        throw new DomainException('Account is already locked.');
    }

    public function canBeDeleted(Account $account): bool
    {
        return true;
    }

    public function delete(Account $account): void
    {
        $account->changeState(new DeletedState());
    }

    public function getPossibleTransitions(): array
    {
        return ['reactivate', 'delete'];
    }
}
