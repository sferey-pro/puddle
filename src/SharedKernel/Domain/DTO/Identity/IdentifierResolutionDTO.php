<?php

namespace SharedKernel\Domain\DTO\Identity;

/**
 * DTO pour transmettre le résultat de résolution aux autres contextes.
 */
final readonly class IdentifierResolutionDTO
{
    public function __construct(
        public string $type,    // 'email', 'phone', etc.
        public string $value,   // La valeur normalisée
        public bool $isValid
    ) {}
}
