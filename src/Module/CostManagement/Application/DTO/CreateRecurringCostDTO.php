<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\DTO;

use App\Module\CostManagement\Domain\Enum\RecurrenceFrequency;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Sequentially;

/**
 * DTO pour le formulaire unifié. Il contient les informations de la planification
 * et un DTO imbriqué pour les données du modèle de coût.
 */
final class CreateRecurringCostDTO
{
    /**
     * @var CreateCostItemDTO contient les données du sous-formulaire pour le modèle
     */
    #[Assert\Valid]
    public CreateCostItemDTO $template;

    #[Sequentially(
        constraints: [
            new Assert\NotBlank(),
            new Assert\Choice(callback: [RecurrenceFrequency::class, 'values']),
        ]
    )]
    public ?string $frequency = '';

    public ?int $day = null;

    public function __construct()
    {
        $this->template = new CreateCostItemDTO();
    }
}
