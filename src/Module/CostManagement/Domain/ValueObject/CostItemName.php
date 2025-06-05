<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\ValueObject;

use Webmozart\Assert\Assert;

/**
 * Représente le nom d'un poste de coût.
 * Ce Value Object garantit que le nom n'est pas vide et respecte une longueur maximale.
 */
final class CostItemName implements \Stringable
{
    public readonly string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value, 'Cost item name cannot be empty.');
        Assert::maxLength($value, 255, 'Cost item name cannot be longer than 255 characters.');

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
