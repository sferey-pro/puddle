<?php

declare(strict_types=1);

namespace Kernel\Domain\ValueObject;

/**
 * Classe de base pour les Value Objects qui ne sont qu'un wrapper
 * autour d'une unique valeur de type string.
 */
abstract readonly class AbstractStringValueObject implements ValueObjectInterface, \Stringable
{
    use ValidatedValueObjectTrait;

    /**
     * Le constructeur est protégé pour forcer la création via une factory
     * nommée (comme `create()` ou `fromString()`) dans les classes enfants,
     * qui contiendra la logique de validation.
     */
    protected function __construct(protected(set) string $value) {}

    /**
     * @return T
     */
    public function value(): mixed
    {
        return $this->value;
    }

    public function equals(ValueObjectInterface $other): bool
    {
        return $other instanceof static && $this->value === $other->value;
    }

    public function __toArray(): array
    {
        return ['value' => $this->value];
    }

    public static function fromArray(array $data): static
    {
        return static::create($data['value']);
    }

    #[\Override]
    public function __toString(): string
    {
        return (string) $this->value;
    }
}
