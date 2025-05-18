<?php

declare(strict_types=1);

namespace App\Core\Enum;

trait EnumArraySerializableTrait
{
    use EnumNamesTrait;
    use EnumValuesTrait;

    public static function array(): array
    {
        return array_combine(static::names(), static::values());
    }
}
