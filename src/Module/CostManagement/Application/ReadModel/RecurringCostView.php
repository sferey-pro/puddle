<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\ReadModel;

use App\Module\CostManagement\Domain\Enum\RecurringCostStatus;

/**
 * Read Model pour une planification de coût récurrent.
 * C'est une représentation plate des données, optimisée pour l'affichage.
 */
class RecurringCostView
{
    public function __construct(
        public string $id,
        public string $templateCostItemId,
        public string $templateName,
        public string $recurrenceRule,
        public RecurringCostStatus $status,
        public ?\DateTimeImmutable $nextGenerationDate,
        public ?\DateTimeImmutable $lastGeneratedAt,
    ) {
    }
}
