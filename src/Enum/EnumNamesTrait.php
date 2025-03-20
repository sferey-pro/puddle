<?php

declare(strict_types=1);

namespace App\Enum;

trait EnumNamesTrait
{
    abstract public static function cases(): array;

    public static function names(): array
    {
        return array_map(fn ($enum) => $enum->name, static::cases());
    }
}