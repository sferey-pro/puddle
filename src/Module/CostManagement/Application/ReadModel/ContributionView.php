<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\ReadModel;

/**
 * Représentation simplifiée d'une contribution pour le Read Model.
 * Ce DTO n'est pas persisté directement mais embarqué dans CostItemView.
 */
class ContributionView
{
    public function __construct(
        public readonly string $id,
        public readonly float $amount,
        public readonly string $currency,
        public readonly \DateTimeImmutable $contributedAt,
        public readonly ?string $sourceProductId = null,
    ) {
    }
}
