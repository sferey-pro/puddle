<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use Symfony\Component\Uid\Uuid;

abstract class Uid implements \Stringable
{
    final public function __construct(protected string $value)
    {
        $this->ensureIsValidUuid($value);
    }

    final public static function random(): self
    {
        return new static(Uuid::v4()->toString());
    }

    final public function value(): string
    {
        return $this->value;
    }

    final public function equals(self $other): bool
    {
        return $this->value() === $other->value();
    }

    public function __toString(): string
    {
        return $this->value();
    }

    private function ensureIsValidUuid(string $id): void
    {
        if (!Uuid::isValid($id)) {
            throw new \InvalidArgumentException(\sprintf('<%s> does not allow the value <%s>.', self::class, $id));
        }
    }
}
