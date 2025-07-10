<?php

declare(strict_types=1);

namespace Kernel\Domain\Saga;

use Kernel\Domain\ValueObject\AggregateRootId;

/**
 * Représente l'identifiant unique d'un parcours métier (Saga).
 */
final class SagaStateId extends AggregateRootId
{
}
