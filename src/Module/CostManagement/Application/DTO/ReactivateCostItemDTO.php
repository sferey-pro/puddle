<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO for reactivating a Cost Item.
 */
final class ReactivateCostItemDTO
{
    #[Assert\NotBlank]
    public ?string $costItemId = '';
}
