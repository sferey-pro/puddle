<?php

declare(strict_types=1);

namespace App\Core\Domain\Saga;

use App\Core\Domain\ValueObject\AggregateRootId;

/**
 * Représente l'identifiant unique d'un parcours métier (Saga).
 */
final class SagaStateId extends AggregateRootId
{
}
