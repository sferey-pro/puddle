<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\DTO;

/**
 * DTO for adding a new Cost Item.
 * Dates are expected in 'Y-m-d' format.
 * Amount is expected in the smallest currency unit (e.g., cents).
 */
final readonly class AddCostItemDTO
{
    public function __construct(
        public string $name,
        public int $targetAmount,
        public string $currency, // e.g., "EUR"
        public string $startDate, // Format: "Y-m-d"
        public string $endDate,   // Format: "Y-m-d"
        public ?string $userId = null // Optional: ID of the user performing the action
    ) {
    }
}
