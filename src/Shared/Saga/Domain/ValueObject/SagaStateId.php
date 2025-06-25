<?php

declare(strict_types=1);

namespace App\Shared\Saga\Domain\ValueObject;

use App\Shared\Domain\ValueObject\AggregateRootId;

/**
 * L'identifiant unique pour une instance de Saga.
 * Chaque ID représente une exécution unique et traçable d'un processus métier,
 * comme une inscription d'utilisateur spécifique ou le traitement d'une commande particulière.
 */
final class SagaStateId extends AggregateRootId
{
}
