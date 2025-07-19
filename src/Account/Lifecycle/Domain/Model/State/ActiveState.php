<?php

declare(strict_types=1);

namespace Account\Lifecycle\Domain\Model\State;

use Account\Core\Domain\Model\Account;
use Account\Lifecycle\Domain\Model\AccountState;

final class ActiveState implements AccountState
{
    public function getName(): string
    {
        return 'active';
    }

    public function canBeVerified(Account $account): bool
    {
        return false; // Déjà vérifié
    }

    public function verify(Account $account): void
    {
        throw new \DomainException('Account is already verified');
    }

    public function canBeSuspended(Account $account): bool
    {
        return true;
    }

    public function suspend(Account $account, string $reason): void
    {
        $account->changeState(new SuspendedState($reason));
    }

    public function canBeReactivated(Account $account): bool
    {
        return false; // Déjà actif
    }

    public function reactivate(Account $account): void
    {
        throw new \DomainException('Account is already active');
    }

    public function canBeLocked(Account $account): bool
    {
        return true;
    }

    public function lock(Account $account, string $reason): void
    {
        $account->changeState(new LockedState($reason));
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
        return ['suspend', 'lock', 'delete', 'deactivate'];
    }
}
