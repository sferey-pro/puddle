<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO pour la création d'une nouvelle contribution.
 */
final class AddContributionDTO
{
    #[Assert\NotBlank]
    public ?string $costItemId = '';

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?float $amount = 0.0;

    // Optionnel, pour la traçabilité future avec le module Sales
    public ?string $sourceProductId = null;
}
