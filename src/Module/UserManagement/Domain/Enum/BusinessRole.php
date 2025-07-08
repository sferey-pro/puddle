<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Enum;

use App\Core\Domain\Enum\EnumJsonSerializableTrait;

enum BusinessRole: string
{
    use EnumJsonSerializableTrait;

    case CONTRIBUTOR = 'contributor';
    case MODERATOR = 'moderator';
    case MANAGER = 'manager';
}
