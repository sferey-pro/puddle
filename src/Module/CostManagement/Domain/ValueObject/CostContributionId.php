<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\ValueObject;

use App\Core\Domain\ValueObject\AggregateRootId;

/**
 * Représente l'identifiant unique d'une contribution à un poste de coût (Aggregat CostContribution).
 */
final class CostContributionId extends AggregateRootId
{
}
