<?php

declare(strict_types=1);

namespace Authentication\Domain\Repository;

use Authentication\Domain\Model\BlockedIP;

interface BlockedIPRepositoryInterface
{
    /**
     * Trouve un blocage par adresse IP.
     */
    public function findByIpAddress(string $ipAddress): ?BlockedIP;

    /**
     * Vérifie si une IP est bloquée.
     */
    public function isBlocked(string $ipAddress): bool;

    /**
     * Persiste un blocage.
     */
    public function save(BlockedIP $blockedIP): void;

    /**
     * Supprime un blocage.
     */
    public function remove(BlockedIP $blockedIP): void;

    /**
     * Nettoie les blocages expirés.
     *
     * @return int Nombre de blocages supprimés
     */
    public function removeExpired(): int;

    /**
     * Liste tous les blocages actifs.
     *
     * @return BlockedIP[]
     */
    public function findAllActive(): array;
}
