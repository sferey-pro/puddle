<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Domain\Enum;

use App\Core\Enum\EnumJsonSerializableTrait;

enum UnitOfMeasure: string
{
    use EnumJsonSerializableTrait;

    case GRAM = 'g';
    case KILOGRAM = 'kg';
    case MILLILITER = 'ml';
    case CENTILITER = 'cl';
    case LITER = 'l';
    case PIECE = 'pc'; // Pour les articles comptés à la pièce

    public function getLabel(): string
    {
        return match ($this) {
            self::GRAM => 'Gramme(s)',
            self::KILOGRAM => 'Kilogramme(s)',
            self::MILLILITER => 'Millilitre(s)',
            self::CENTILITER => 'Centilitre(s)',
            self::LITER => 'Litre(s)',
            self::PIECE => 'Pièce(s)',
        };
    }

    /**
     * Facteur pour convertir vers une unité de base (ex: g pour masse, ml pour volume).
     */
    public function getBaseFactor(): float
    {
        return match ($this) {
            self::GRAM, self::MILLILITER, self::PIECE => 1.0,
            self::KILOGRAM => 1000.0, // 1 kg = 1000 g
            self::LITER => 1000.0,    // 1 L = 1000 ml
            self::CENTILITER => 10.0,  // 1 cl = 10 ml
        };
    }

    public function getBaseUnitType(): string // Pour regrouper (masse, volume, compte)
    {
        return match ($this) {
            self::GRAM, self::KILOGRAM => 'mass',
            self::MILLILITER, self::LITER, self::CENTILITER => 'volume',
            self::PIECE => 'count',
        };
    }

    public static function tryFromSymbol(string $symbol): ?self
    {
        foreach (self::cases() as $case) {
            if (mb_strtolower($case->value) === mb_strtolower($symbol)) {
                return $case;
            }
        }

        return null;
    }
}
