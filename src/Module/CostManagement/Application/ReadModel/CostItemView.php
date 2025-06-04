<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\ReadModel;

/**
 * Read model representing a CostItem for display purposes.
 */
final readonly class CostItemView
{
    public function __construct(
        public string $id,
        public string $name,
        public float $targetAmount, // Display-friendly format
        public float $currentAmount, // Display-friendly format
        public string $currency,
        public string $startDate,    // Format: "Y-m-d"
        public string $endDate,      // Format: "Y-m-d"
        public string $status,       // 'active', 'covered', 'archived'
        public float $progressPercentage, // Calculated: (currentAmount / targetAmount) * 100
        public bool $isCovered,
        public bool $isActiveNow
    ) {
    }
}
