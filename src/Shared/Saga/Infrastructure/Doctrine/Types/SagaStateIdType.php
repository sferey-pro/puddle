<?php

declare(strict_types=1);

namespace App\Shared\Saga\Infrastructure\Doctrine\Types;

use App\Shared\Infrastructure\Doctrine\Types\AbstractValueObjectIdType;
use App\Shared\Saga\Domain\ValueObject\SagaStateId;

/**
 * Classe de type Doctrine pour le ValueObject UserId.
 * Permet à Doctrine de comprendre comment stocker et récupérer cet objet.
 */
final class SagaStateIdType extends AbstractValueObjectIdType
{
    /**
     * Le nom unique de notre type pour Doctrine.
     */
    public const TYPE_NAME = 'saga_state_id';

    public function getName(): string
    {
        return self::TYPE_NAME;
    }

    /**
     * Spécifie la classe du ValueObject que ce type gère.
     */
    protected function getValueObjectClass(): string
    {
        return SagaStateId::class;
    }

}
