<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Enum;

use App\Core\Domain\Enum\EnumJsonSerializableTrait;

enum SocialNetwork: string
{
    use EnumJsonSerializableTrait;

    case GOOGLE = 'google_main';
    case GITHUB = 'github_main';

    public function getLabel(): string
    {
        return match ($this) {
            self::GOOGLE => 'Google',
            self::GITHUB => 'Github',
        };
    }

    /**
     * @return array{label: string, color: string}
     */
    public function getBadgeConfiguration(): array
    {
        return [
            'label' => $this->getLabel(),
            'color' => match ($this) {
                self::GOOGLE => 'blue',
                self::GITHUB => 'black',
            },
        ];
    }

    public function equals(self $other): bool
    {
        return $this === $other;
    }
}
