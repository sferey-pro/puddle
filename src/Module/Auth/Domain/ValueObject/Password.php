<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\ValueObject;

use Webmozart\Assert\Assert;

final readonly class Password
{
    public readonly ?string $value;

    public function __construct(?string $value)
    {
        Assert::notEmpty($value, 'Password cannot be empty.');
        $this->value = $value;
    }

    public static function random(): self
    {
        return new self(md5(random_bytes(10)));
    }
}
