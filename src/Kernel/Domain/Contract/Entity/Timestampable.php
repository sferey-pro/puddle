<?php

declare(strict_types=1);

namespace Kernel\Domain\Contract\Entity;

/**
 * Interface pour les entités avec timestamps.
 * Le domaine définit le contrat, l'infrastructure l'implémente.
 */
interface Timestampable
{
    public function getCreatedAt(): \DateTimeImmutable;
    public function getUpdatedAt(): \DateTimeImmutable;
}
