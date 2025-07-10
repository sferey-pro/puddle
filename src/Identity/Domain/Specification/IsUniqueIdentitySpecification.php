<?php

declare(strict_types=1);

namespace Identity\Domain\Specification;

use Identity\Domain\ValueObject\Identifier;
use Kernel\Domain\Specification\SpecificationInterface;

/**
 * Specification qui vérifie si un Identifier n'est pas déjà utilisé.
 */
final class IsUniqueIdentitySpecification implements SpecificationInterface
{
    public function __construct(
        public readonly Identifier $identifier
    ) {
    }

    public function failureReason(): ?string {
        return null;
    }

    public function isSatisfiedBy(mixed $candidate): bool
    {
        // La logique est dans l'implémentation du repository (côté infrastructure)
        return true;
    }
}
