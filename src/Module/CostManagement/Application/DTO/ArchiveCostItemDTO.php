<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\DTO;

use App\Shared\Application\DTO\AbstractDTO;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO pour l'archivage d'un Cost Item.
 */
final class ArchiveCostItemDTO extends AbstractDTO
{
    #[Assert\NotBlank]
    public ?string $costItemId;
}
