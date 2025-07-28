<?php

declare(strict_types=1);

namespace Identity\Domain\Service;

use Kernel\Domain\Result;

/**
 * Interface pour interagir avec Identity depuis Account/Registration
 */
interface IdentifierResolverInterface
{
    public function resolve(string $identifier): Result;

    /**
     * Détecte le type d'un identifiant.
     */
    public function detectType(string $value): ?string;
}
