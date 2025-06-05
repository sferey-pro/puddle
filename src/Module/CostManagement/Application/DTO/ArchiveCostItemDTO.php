<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\DTO;

/**
 * DTO for archiving a Cost Item.
 */
final readonly class ArchiveCostItemDTO
{
    public function __construct(
        public string $costItemId,
    ) {
    }
}
