<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Infrastructure\Doctrine\Types;

use App\Module\ProductCatalog\Domain\ValueObject\UnitOfMeasure;
use App\Shared\Infrastructure\Doctrine\Types\AbstractEnumType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class UnitOfMeasureType extends AbstractEnumType
{
    public const NAME = 'unit_of_measure_enum';

    public static function getEnumsClass(): string
    {
        return UnitOfMeasure::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        // Vous pouvez stocker cela comme une chaîne de longueur appropriée
        return $platform->getStringTypeDeclarationSQL(['length' => '10']); // ex: 'kg', 'piece'
    }
}
