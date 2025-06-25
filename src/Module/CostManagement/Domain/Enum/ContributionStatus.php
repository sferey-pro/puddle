<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Enum;

use App\Core\Domain\Enum\EnumArraySerializableTrait;

/**
 * Définit le statut d'une contribution.
 * Une contribution peut être active ou annulée pour garantir la traçabilité.
 */
enum ContributionStatus: string
{
    use EnumArraySerializableTrait;

    case ACTIVE = 'active';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::CANCELLED => 'Annulé',
        };
    }
}
