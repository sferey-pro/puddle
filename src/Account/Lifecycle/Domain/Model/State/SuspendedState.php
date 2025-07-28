<?php

declare(strict_types=1);

namespace Account\Lifecycle\Domain\Model\State;

use Account\Core\Domain\Model\Account;
use Account\Lifecycle\Domain\Model\AccountState;

final readonly class SuspendedState implements AccountState
{
    public function __construct(
        private(set) string $reason,
        private(set) ?\DateTimeImmutable $until = null
    ) {
    }

    public function getName(): string
    {
        return 'suspended';
    }



    public function canBeVerified(Account $account): bool
    {
        return false;
    }

    public function verify(Account $account): void
    {
        throw new \DomainException('Cannot verify a suspended account');
    }

    public function canBeSuspended(Account $account): bool
    {
        return false; // Déjà suspendu
    }

    public function suspend(Account $account, string $reason): void
    {
        throw new \DomainException('Account is already suspended');
    }

    public function canBeReactivated(Account $account): bool
    {
        // Peut être réactivé si la suspension n'a pas de date de fin
        // ou si la date est passée
        if ($this->until === null) {
            return true;
        }

        return $this->until <= new \DateTimeImmutable();
    }

    public function reactivate(Account $account): void
    {
        if (!$this->canBeReactivated($account)) {
            throw new \DomainException(
                sprintf('Account cannot be reactivated until %s',
                    $this->until->format('Y-m-d H:i:s')
                )
            );
        }

        $account->changeState(new ActiveState());
    }

    public function canBeLocked(Account $account): bool
    {
        return true; // Peut passer de suspendu à verrouillé
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
