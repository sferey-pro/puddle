<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Enum;

use App\Core\Enum\EnumJsonSerializableTrait;

/**
 * Représente les différents statuts qu'un poste de coût (CostItem) peut avoir.
 *
 * - ACTIVE: Le poste de coût est en cours et peut recevoir des contributions.
 * - FULLY_COVERED: L'objectif de coût a été atteint.
 * - ARCHIVED: Le poste de coût est archivé et n'est plus modifiable (sauf pour réactivation).
 */
enum CostItemStatus: string
{
    use EnumJsonSerializableTrait;

    case ACTIVE = 'active';
    case FULLY_COVERED = 'fully_covered';
    case ARCHIVED = 'archived';

    public function equals(self $other): bool
    {
        return $this === $other;
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => 'Actif',
            self::FULLY_COVERED => 'Entièrement Couvert',
            self::ARCHIVED => 'Archivé',
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
                self::ACTIVE => 'blue',
                self::FULLY_COVERED => 'green',
                self::ARCHIVED => 'orange',
            }
        ];
    }
}
