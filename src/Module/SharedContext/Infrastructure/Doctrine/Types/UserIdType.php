<?php

declare(strict_types=1);

namespace App\Module\SharedContext\Infrastructure\Doctrine\Types;

use App\Module\SharedContext\Domain\ValueObject\UserId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

/**
 * Classe de type Doctrine pour le ValueObject UserId.
 * Permet à Doctrine de comprendre comment stocker et récupérer cet objet.
 */
final class UserIdType extends AbstractUidType
{
    /**
     * Le nom unique de notre type pour Doctrine.
     */
    public const TYPE_NAME = 'user_id';

    public function getName(): string
    {
        return self::TYPE_NAME;
    }

    /**
     * Spécifie la classe du ValueObject que ce type gère.
     */
    protected function getUidClass(): string
    {
        return UserId::class;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        // Force le type de la colonne en base de données à être un UUID
        return $platform->getGuidTypeDeclarationSQL($column);
    }
}
