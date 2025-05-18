<?php

declare(strict_types=1);

namespace App\Module\Shared\Domain\ValueObject;

use Webmozart\Assert\Assert;

final class Email implements \Stringable
{
    public readonly string $value;

    public function __construct(string $value)
    {
        Assert::lengthBetween($value, 1, 180);

        $this->value = $value;
    }

    public function isEqualTo(self $email): bool
    {
        return $email->value === $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
