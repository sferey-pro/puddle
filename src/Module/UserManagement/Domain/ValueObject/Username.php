<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\ValueObject;

use Webmozart\Assert\Assert;

final readonly class Username implements \Stringable
{
    public function __construct(public string $value)
    {
        Assert::lazy()
            ->that($value, 'username')->notBlank('Username cannot be blank.')
            ->that($value, 'username')->minLength(3, 'Username must be at least 3 characters long.')
            ->that($value, 'username')->regex('/^[a-zA-Z0-9_]+$/', 'Username can only contain letters, numbers, and underscores.')
            ->verifyNow();
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
