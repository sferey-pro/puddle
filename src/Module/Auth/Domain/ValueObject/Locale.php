<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\ValueObject;

use App\Shared\Domain\ValueObject\NullableValueObjectInterface;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
final class Locale implements NullableValueObjectInterface
{
    #[ORM\Column(name: 'locale', length: 12, nullable: true)]
    public readonly ?string $value;

    public function __construct(?string $value)
    {
        if (null !== $value) {
            Assert::lengthBetween($value, 1, 180);
        }

        $this->value = $value;
    }
}
