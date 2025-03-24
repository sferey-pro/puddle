<?php

declare(strict_types=1);

namespace App\Config;

use App\Enum\EnumJsonSerializableTrait;

enum SocialNetwork: string
{
    use EnumJsonSerializableTrait;

    case GOOGLE = 'google_main';
    case GITHUB = 'github_main';
}
