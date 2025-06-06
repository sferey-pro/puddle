<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\DTO;

use App\Shared\Application\DTO\AbstractDTO;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO for reactivating a Cost Item.
 */
final class ReactivateCostItemDTO extends AbstractDTO
{
    #[Assert\NotBlank]
    public ?string $costItemId;
}
