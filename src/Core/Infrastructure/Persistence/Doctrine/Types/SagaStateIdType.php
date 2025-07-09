<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine\Types;

use App\Core\Domain\Saga\SagaStateId;
use App\Core\Infrastructure\Persistence\Doctrine\Types\AbstractValueObjectIdType;

/**
 * Classe de type Doctrine pour le ValueObject SagaStateId.
 * Permet à Doctrine de comprendre comment stocker et récupérer cet objet.
 */
final class SagaStateIdType extends AbstractValueObjectIdType
{
    /**
     * Le nom unique de notre type pour Doctrine.
     */
    public const NAME = 'saga_state_id';

    /**
     * Spécifie la classe du ValueObject que ce type gère.
     */
    protected function getValueObjectClass(): string
    {
        return SagaStateId::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
