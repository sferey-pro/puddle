<?php

declare(strict_types=1);

namespace Account\Lifecycle\Domain\Model\State;

use Account\Core\Domain\Model\Account;
use Account\Lifecycle\Domain\Model\AccountState;

final readonly class InactiveState implements AccountState
{
    public function __construct(
        private(set) \DateTimeImmutable $inactiveSince
    ) {
    }

    public function getName(): string
    {
        return 'inactive';
    }

    public function canBeVerified(Account $account): bool
    {
        return false;
    }

    public function verify(Account $account): void
    {
        throw new \DomainException('Cannot verify an inactive account');
    }

    public function canBeSuspended(Account $account): bool
    {
        return false;
    }

    public function suspend(Account $account, string $reason): void
    {
        throw new \DomainException('Cannot suspend an inactive account');
    }

    public function canBeReactivated(Account $account): bool
    {
        return true;
    }

    public function reactivate(Account $account): void
    {
        $account->changeState(new ActiveState());
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
        return ['reactivate', 'lock', 'delete'];
    }
}
