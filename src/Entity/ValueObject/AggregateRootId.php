<?php

declare(strict_types=1);

namespace App\Entity\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\Uuid;

trait AggregateRootId
{
    #[ORM\Column(name: 'uuid', type: UuidType::NAME)]
    public readonly AbstractUid $value;

    final public function __construct(?AbstractUid $value = null)
    {
        $this->value = $value ?? Uuid::v7();
    }

    public static function fromString(string $uuid) {
        return new self(Uuid::fromString($uuid));
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
