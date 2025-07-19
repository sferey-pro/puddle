<?php

declare(strict_types=1);

namespace SharedKernel\Infrastructure\Persistence\Doctrine\Types;

use Kernel\Infrastructure\Persistence\Doctrine\Types\AbstractValueObjectIdType;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Classe de type Doctrine pour le ValueObject UserId.
 * Permet à Doctrine de comprendre comment stocker et récupérer cet objet.
 */
final class UserIdType extends AbstractValueObjectIdType
{
    /**
     * Le nom unique de notre type pour Doctrine.
     */
    public const string NAME = 'user_id';

    /**
     * Spécifie la classe du ValueObject que ce type gère.
     */
    protected function getValueObjectClass(): string
    {
        return UserId::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
