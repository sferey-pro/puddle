<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Domain\ValueObject;

use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

final class Quantity implements \Stringable
{
    public readonly float $value;
    public readonly UnitOfMeasure $unit;

    public function __construct(float $value, UnitOfMeasure $unit)
    {
        Assert::greaterThanEq($value, 0, 'Quantity value cannot be negative.');
        $this->value = $value;
        $this->unit = $unit;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getUnit(): UnitOfMeasure
    {
        return $this->unit;
    }

    public function isEqualTo(self $other): bool
    {
        // Pour une comparaison stricte, les unités doivent être identiques.
        // Une conversion serait nécessaire pour comparer "1 kg" et "1000 g" comme égaux.
        return $this->value === $other->value && $this->unit === $other->unit;
    }

    public function __toString(): string
    {
        return \sprintf('%s %s', $this->value, $this->unit->value);
    }

    /**
     * Méthode pour convertir vers une unité de base ou une autre unité compatible.
     *
     * @throws InvalidArgumentException
     */
    public function convertTo(UnitOfMeasure $targetUnit): self
    {
        Assert::eq($this->unit->getBaseUnitType(), $targetUnit->getBaseUnitType(), 'Cannot convert between incompatible unit types.');

        if ($this->unit === $targetUnit) {
            return $this;
        }

        $valueInBaseUnit = $this->value * $this->unit->getBaseFactor();
        $newValue = $valueInBaseUnit / $targetUnit->getBaseFactor();

        return new self($newValue, $targetUnit);
    }
}
