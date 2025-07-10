<?php

declare(strict_types=1);

namespace Account\Lifecycle\Domain\State;

use Account\Core\Domain\Account;

final class PendingState implements AccountState
{
    public function getName(): string
    {
        return 'pending';
    }

    public function canBeVerified(Account $account): bool
    {
        return true;
    }

    public function verify(Account $account): void
    {
        $account->changeState(new ActiveState());
        $account->markAsVerified();
    }

    public function canBeSuspended(Account $account): bool
    {
        return false; // Un compte non vérifié ne peut pas être suspendu
    }

    public function suspend(Account $account, string $reason): void
    {
        throw new \DomainException('Cannot suspend a pending account');
    }

    public function canBeReactivated(Account $account): bool
    {
        return false;
    }

    public function reactivate(Account $account): void
    {
        throw new \DomainException('Cannot reactivate a pending account');
    }

    public function canBeLocked(Account $account): bool
    {
        return true; // Peut être verrouillé si trop de tentatives
    }

    public function lock(Account $account, string $reason): void
    {
        $account->changeState(new LockedState($reason));
    }

    public function canBeDeleted(Account $account): bool
    {
        return true; // Peut être supprimé avant vérification
    }

    public function delete(Account $account): void
    {
        $account->changeState(new DeletedState());
        $account->markAsDeleted();
    }

    public function getPossibleTransitions(): array
    {
        return ['verify', 'lock', 'delete'];
    }
}
