<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\DTO;

/**
 * DTO for adding a contribution to a Cost Item.
 * Amount is expected in the smallest currency unit (e.g., cents).
 */
final readonly class AddContributionDTO
{
    public function __construct(
        public string $costItemId,
        public int $amount,
        public string $currency, // e.g., "EUR"
        public ?string $contributorDetails = null, // Optional: Details about the source of contribution (e.g., sale ID, user ID)
    ) {
    }
}
