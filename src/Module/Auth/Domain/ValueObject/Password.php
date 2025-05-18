<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\ValueObject;

use App\Shared\Domain\ValueObject\NullableValueObjectInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class Password implements NullableValueObjectInterface
{
    #[ORM\Column(name: 'password', length: 255, nullable: true)]
    public readonly ?string $value;

    public function __construct(?string $value)
    {
        $this->value = $value;
    }
}
