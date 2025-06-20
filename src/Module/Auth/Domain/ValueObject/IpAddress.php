<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\ValueObject;

use Webmozart\Assert\Assert;

final readonly class IpAddress implements \Stringable
{
    public readonly ?string $value;

    public function __construct(?string $value)
    {
        Assert::lengthBetween($value, 1, 32);

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
