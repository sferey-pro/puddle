<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\ValueObject;

use Webmozart\Assert\Assert;

final readonly class Locale implements \Stringable
{
    public readonly ?string $value;

    public function __construct(?string $value)
    {
        if (null !== $value) {
            Assert::lengthBetween($value, 1, 180);
        }

        $this->value = $value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
