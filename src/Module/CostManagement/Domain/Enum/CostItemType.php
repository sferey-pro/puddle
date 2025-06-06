<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Enum;

use App\Core\Enum\EnumArraySerializableTrait;

/**
 * Définit la typologie d'un poste de coût (CostItem).
 * Cette classification permet de distinguer les coûts selon leur nature (direct/indirect)
 * et leur comportement (fixe/variable), ce qui est essentiel pour les futures
 * stratégies d'allocation et les analyses financières.
 */
enum CostItemType: string
{
    use EnumArraySerializableTrait;

    case DIRECT_FIXED = 'direct_fixed';
    case DIRECT_VARIABLE = 'direct_variable';
    case INDIRECT_FIXED = 'indirect_fixed';
    case INDIRECT_VARIABLE = 'indirect_variable';

    public function getLabel(): string
    {
        return match ($this) {
            self::DIRECT_FIXED => 'Coût Fixe Direct',
            self::DIRECT_VARIABLE => 'Coût Variable Direct',
            self::INDIRECT_FIXED => 'Coût Fixe Indirect',
            self::INDIRECT_VARIABLE => 'Coût Variable Indirect',
        };
    }
}
