<?php

declare(strict_types=1);

namespace App\Module\SharedContext\Infrastructure\Doctrine\Types;

use App\Core\Infrastructure\Persistence\Doctrine\Types\AbstractValueObjectIdType;
use App\Module\SharedContext\Domain\ValueObject\ProductId;

/**
 * Classe de type Doctrine pour le ValueObject ProductId.
 * Permet à Doctrine de comprendre comment stocker et récupérer cet objet.
 */
final class ProductIdType extends AbstractValueObjectIdType
{
    /**
     * Le nom unique de notre type pour Doctrine.
     */
    public const NAME = 'product_id';

    /**
     * Spécifie la classe du ValueObject que ce type gère.
     */
    protected function getValueObjectClass(): string
    {
        return ProductId::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
