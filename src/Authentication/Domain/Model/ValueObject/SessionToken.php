<?php

declare(strict_types=1);

namespace App\Authentication\Domain\Model\ValueObject;

use Symfony\Component\Uid\Uuid;

final readonly class SessionToken
{
    private string $value;

    private function __construct(string $value)
    {
        if (strlen($value) < 32) {
            throw new \InvalidArgumentException('Session token must be at least 32 characters');
        }
        $this->value = $value;
    }

    public static function generate(): self
    {
        return new self(
            base64_encode(hash('sha256', Uuid::v4() . random_bytes(32), true))
        );
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return hash_equals($this->value, $other->value);
    }
}
