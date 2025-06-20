<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\ValueObject;

final readonly class Hash implements \Stringable
{
    public readonly ?string $value;

    public function __construct(?string $value)
    {
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
