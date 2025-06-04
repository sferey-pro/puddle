<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\DTO;

/**
 * DTO for reactivating a Cost Item.
 */
final readonly class ReactivateCostItemDTO
{
    public function __construct(
        public string $costItemId
    ) {
    }
}
