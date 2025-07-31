<?php

declare(strict_types=1);

namespace Identity\Domain\Service;

use Identity\Domain\ValueObject\Identifier;
use Kernel\Domain\Result;

/**
 * Interface pour interagir avec Identity depuis Account/Registration
 */
interface IdentifierResolverInterface
{
    public function resolve(string $identifier): Identifier;

    public function tryResolveIdentifier(string $identifier): ?Identifier;
    
    public function resolveMultiple(array $identifiers): array;
}
