<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Infrastructure\Doctrine\Types;

use App\Core\Infrastructure\Persistence\Doctrine\Types\AbstractValueObjectIdType;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;

/**
 * Classe de type Doctrine pour le ValueObject CostItemId.
 * Permet à Doctrine de comprendre comment stocker et récupérer cet objet.
 */
final class CostItemIdType extends AbstractValueObjectIdType
{
    /**
     * Le nom unique de notre type pour Doctrine.
     */
    public const NAME = 'cost_item_id';

    /**
     * Spécifie la classe du ValueObject que ce type gère.
     */
    protected function getValueObjectClass(): string
    {
        return CostItemId::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
