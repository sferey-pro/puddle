<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Enum;

use App\Core\Domain\Enum\EnumJsonSerializableTrait;

enum IdentifierType: string
{
    use EnumJsonSerializableTrait;

    case EMAIL = 'email';
    case PHONE = 'phone';

    public function equals(self $other): bool
    {
        return $this === $other;
    }
}
