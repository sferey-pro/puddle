<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\ValueObject;

final readonly class Password
{
    public readonly ?string $value;

    public function __construct(?string $value)
    {
        $this->value = $value;
    }

    public static function random(): self
    {
        return new self(md5(random_bytes(10)));
    }
}
