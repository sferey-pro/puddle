<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Enum;

use App\Core\Domain\Enum\EnumArraySerializableTrait;

enum RecurringCostStatus: string
{
    use EnumArraySerializableTrait;

    case ACTIVE = 'active';
    case PAUSED = 'paused';

    public function equals(self $other): bool
    {
        return $this === $other;
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => 'Actif',
            self::PAUSED => 'En pause',
        };
    }

    /**
     * @return array{label: string, color: string, dot: bool}
     */
    public function getBadgeConfiguration(): array
    {
        return [
            'label' => $this->getLabel(),
            'color' => match ($this) {
                self::ACTIVE => 'green',
                self::PAUSED => 'gray',
            },
        ];
    }
}
