<?php

declare(strict_types=1);

namespace Account\Registration\Application\Service;

use Kernel\Domain\Result;

/**
 * Anti-Corruption Layer : Définit comment le contexte Account/Registration
 * a besoin de résoudre une identité.
 */
interface IdentifierResolverInterface
{
    public function resolve(string $value): Result;
}
