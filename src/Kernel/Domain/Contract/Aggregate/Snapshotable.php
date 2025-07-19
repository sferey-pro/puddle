<?php

namespace Kernel\Domain\Contract\Aggregate;

interface Snapshotable
{
    /**
     * Crée un snapshot de l'état actuel de l'agrégat.
     */
    public function toSnapshot(): array;

    /**
     * Reconstitue un agrégat à partir d'un snapshot.
     */
    public static function fromSnapshot(array $data): static;

    /**
     * Retourne la version du snapshot.
     */
    public function getSnapshotVersion(): int;
}
