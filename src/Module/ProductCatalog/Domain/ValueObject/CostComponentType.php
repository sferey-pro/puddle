<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Domain\ValueObject;

use App\Core\Enum\EnumJsonSerializableTrait;

enum CostComponentType: string
{
    use EnumJsonSerializableTrait;

    case RAW_MATERIAL = 'raw_material'; // Matière première (ex: grain de café, lait)
    case DIRECT_LABOR = 'direct_labor'; // Coût de main d'œuvre directe pour ce produit
    case PACKAGING = 'packaging'; // Emballage
    case OPERATIONAL_FIXED_ALLOCATED = 'operational_fixed_allocated'; // Part d'un coût fixe opérationnel alloué (ex: part du loyer)
    case OPERATIONAL_VARIABLE_ALLOCATED = 'operational_variable_allocated'; // Part d'un coût variable alloué (ex: part de la facture d'eau basée sur la consommation)
    case TAX_FEE = 'tax_fee'; // Impôts et taxes spécifiques au produit ou à la vente
    case MARGIN_CONTINGENCY = 'margin_contingency'; // Marge ou provision pour imprévus
    case SALARY_ALLOCATED = 'salary_allocated'; // Part du salaire allouée

    public function getLabel(): string
    {
        return match ($this) {
            self::RAW_MATERIAL => 'Matière Première',
            self::DIRECT_LABOR => 'Coût de Main d\'œuvre directe',
            self::PACKAGING => 'Emballage',
            self::OPERATIONAL_FIXED_ALLOCATED => 'Part fixe opérationnelle allouée',
            self::OPERATIONAL_VARIABLE_ALLOCATED => 'Part variable opérationnelle allouée',
            self::TAX_FEE => 'Impôts et taxes spécifiques',
            self::MARGIN_CONTINGENCY => 'Marge ou provision pour imprévus',
            self::SALARY_ALLOCATED => 'Part du salaire allouée',
        };
    }
}
