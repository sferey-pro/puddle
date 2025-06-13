<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Infrastructure\Doctrine\Types;

use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Classe de type Doctrine pour le ValueObject CostItemId.
 * Permet à Doctrine de comprendre comment stocker et récupérer cet objet.
 */
final class CostItemIdType extends AbstractUidType
{
    /**
     * Le nom unique de notre type pour Doctrine.
     */
    public const TYPE_NAME = 'cost_item_id';

    public function getName(): string
    {
        return self::TYPE_NAME;
    }

    /**
     * Spécifie la classe du ValueObject que ce type gère.
     */
    protected function getUidClass(): string
    {
        return CostItemId::class;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        // Force le type de la colonne en base de données à être un UUID
        return $platform->getGuidTypeDeclarationSQL($column);
    }
}
