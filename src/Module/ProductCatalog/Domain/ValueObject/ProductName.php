<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Domain\ValueObject;

use Webmozart\Assert\Assert;

final class ProductName implements \Stringable
{
    public readonly string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value, 'Product name cannot be empty.');
        Assert::maxLength($value, 100, 'Product name cannot be longer than 100 characters.');
        // Autres assertions si nécessaire (ex: caractères autorisés)

        $this->value = $value;
    }

    public function isEqualTo(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
