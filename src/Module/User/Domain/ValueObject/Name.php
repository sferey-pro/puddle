<?php

declare(strict_types=1);

namespace App\Module\User\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
final class Name implements \Stringable
{
    #[ORM\Column(name: 'name', length: 180)]
    public readonly string $value;

    public function __construct(string $value)
    {
        Assert::lengthBetween($value, 1, 180);

        $this->value = $value;
    }

    public function isEqualTo(self $name): bool
    {
        return $name->value === $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
