<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\Uuid;

trait AggregateRootId
{
    final private function __construct(
        public readonly AbstractUid $value,
    ) {
    }

    public static function generate(): self
    {
        return new self(Uuid::v7());
    }

    public static function fromString(string $uuid)
    {
        return new self(Uuid::fromString($uuid));
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
