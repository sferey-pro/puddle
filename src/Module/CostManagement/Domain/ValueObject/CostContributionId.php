<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\ValueObject;

use App\Module\SharedContext\Domain\ValueObject\Ulid;
use App\Shared\Domain\ValueObject\AggregateRootId;

/**
 * Représente l'identifiant unique d'une contribution à un poste de coût.
 * Utilise un ULID pour garantir l'unicité et la triabilité chronologique.
 */
final readonly class CostContributionId implements \Stringable
{
    use AggregateRootId;
}
