<?php

declare(strict_types=1);

namespace App\Core\Domain\ValueObject;

/**
 * Classe de base pour les Value Objects qui ne sont qu'un wrapper
 * autour d'une unique valeur de type string.
 */
abstract readonly class AbstractStringValueObject implements \Stringable
{
    /**
     * Le constructeur est protégé pour forcer la création via une factory
     * nommée (comme `create()` ou `fromString()`) dans les classes enfants,
     * qui contiendra la logique de validation.
     */
    protected function __construct(protected(set) string $value) {}

    public function equals(self $other): bool
    {
        return $this->value === $other->value && static::class === $other::class;
    }

    #[\Override]
    public function __toString(): string
    {
        return (string) $this->value;
    }
}
