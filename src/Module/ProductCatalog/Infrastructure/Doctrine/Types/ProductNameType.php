<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Infrastructure\Doctrine\Types;

use App\Core\Infrastructure\Persistence\Doctrine\Types\AbstractValueObjectStringType;
use App\Module\ProductCatalog\Domain\ValueObject\ProductName;

final class ProductNameType extends AbstractValueObjectStringType
{
    /**
     * Le nom unique de notre type pour Doctrine.
     */
    public const NAME = 'product_name';

    /**
     * Spécifie la classe du ValueObject que ce type gère.
     */
    protected function getValueObjectClass(): string
    {
        return ProductName::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
