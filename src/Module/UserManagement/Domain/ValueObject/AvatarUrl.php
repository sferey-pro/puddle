<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\ValueObject;

final readonly class AvatarUrl implements \Stringable
{
    public string $value;

    public function __construct(string $url)
    {
        $this->value = $url;
    }

    public function isEqualTo(self $url): bool
    {
        return $url->value === $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
