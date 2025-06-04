<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\DTO;

/**
 * DTO for updating a Cost Item's details.
 * All fields are optional; only non-null fields will be considered for update.
 * Dates are expected in 'Y-m-d' format if provided.
 * Amount is expected in the smallest currency unit (e.g., cents) if provided.
 */
final readonly class UpdateCostItemDetailsDTO
{
    public function __construct(
        public string $costItemId,
        public ?string $name = null,
        public ?int $targetAmount = null,
        public ?string $currency = null, // Should be provided if targetAmount is updated
        public ?string $startDate = null, // Format: "Y-m-d"
        public ?string $endDate = null    // Format: "Y-m-d"
    ) {
        if ($this->targetAmount !== null && $this->currency === null) {
            throw new \InvalidArgumentException('Currency must be provided when targetAmount is updated.');
        }

        if (($this->startDate !== null || $this->endDate !== null) && !($this->startDate !== null && $this->endDate !== null)) {
            throw new \InvalidArgumentException('Both startDate and endDate must be provided to update the coverage period, or neither.');
        }
    }
}
