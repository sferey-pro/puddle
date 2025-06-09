<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO pour la création ou la mise à jour d'une contribution.
 */
final class AddContributionDTO
{
    // L'ID du CostItem parent, toujours nécessaire
    public ?string $costItemId = null;

    // L'ID de la contribution, seulement pour la mise à jour
    public ?string $contributionId = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?float $amount = 0.0;

    // Optionnel, pour la traçabilité future avec le module Sales
    public ?string $sourceProductId = null;
}
