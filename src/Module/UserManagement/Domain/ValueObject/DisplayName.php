<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\ValueObject;

use Webmozart\Assert\Assert;

final readonly class DisplayName implements \Stringable
{
    public function __construct(public string $value)
    {
        Assert::that($value, 'displayName')->notBlank('Display name cannot be blank.');
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
