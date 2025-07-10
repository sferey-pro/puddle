<?php

declare(strict_types=1);

namespace Account\Lifecycle\Domain\State;

use Account\Core\Domain\Account;

/**
 * Interface pour le pattern State du cycle de vie Account.
 * Chaque état définit ses transitions possibles et comportements.
 */
interface AccountState
{
    /**
     * Retourne le nom de l'état.
     */
    public function getName(): string;

    /**
     * Vérifie si la vérification est possible.
     */
    public function canBeVerified(Account $account): bool;

    /**
     * Effectue la vérification.
     */
    public function verify(Account $account): void;

    /**
     * Vérifie si la suspension est possible.
     */
    public function canBeSuspended(Account $account): bool;

    /**
     * Effectue la suspension.
     */
    public function suspend(Account $account, string $reason): void;

    /**
     * Vérifie si la réactivation est possible.
     */
    public function canBeReactivated(Account $account): bool;

    /**
     * Effectue la réactivation.
     */
    public function reactivate(Account $account): void;

    /**
     * Vérifie si le verrouillage est possible.
     */
    public function canBeLocked(Account $account): bool;

    /**
     * Effectue le verrouillage.
     */
    public function lock(Account $account, string $reason): void;

    /**
     * Vérifie si la suppression est possible.
     */
    public function canBeDeleted(Account $account): bool;

    /**
     * Effectue la suppression.
     */
    public function delete(Account $account): void;

    /**
     * Retourne les transitions possibles depuis cet état.
     */
    public function getPossibleTransitions(): array;
}
