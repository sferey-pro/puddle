<?php

namespace Kernel\Domain\Contract\Service;

interface Idempotent
{
    /**
     * Génère une clé unique pour détecter les doublons.
     */
    public function getIdempotencyKey(): string;
    
    /**
     * Durée de rétention de l'idempotence (en secondes).
     */
    public function getIdempotencyTtl(): int;
}