<?php

namespace App\Module\Auth\Domain\ValueObject;

use Webmozart\Assert\Assert;

/**
 * Représente un token de réinitialisation après avoir été hashé.
 */
final readonly class HashedToken implements \Stringable
{
    public readonly ?string $value;

    public function __construct(?string $value)
    {
        Assert::notEmpty($value, 'Hashed token cannot be empty.');

        $this->value = $value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
