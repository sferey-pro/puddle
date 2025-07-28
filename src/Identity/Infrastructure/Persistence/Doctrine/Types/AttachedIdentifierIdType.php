<?php

declare(strict_types=1);

namespace Identity\Infrastructure\Persistence\Doctrine\Types;

use Identity\Domain\Model\Identity\AttachedIdentifierId;
use Kernel\Infrastructure\Persistence\Doctrine\Types\AbstractValueObjectIdType;

/**
 * Type Doctrine custom pour le ValueObject AttachedIdentifierId.
 * Permet à Doctrine de comprendre comment stocker et récupérer cet objet.
 */
final class AttachedIdentifierIdType extends AbstractValueObjectIdType
{
    /**
     * Le nom unique de notre type pour Doctrine.
     */
    public const string NAME = 'attached_identifier_id';

    /**
     * Spécifie la classe du ValueObject que ce type gère.
     */
    protected function getValueObjectClass(): string
    {
        return AttachedIdentifierId::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
