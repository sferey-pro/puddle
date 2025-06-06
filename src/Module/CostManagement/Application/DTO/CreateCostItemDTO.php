<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\DTO;

use App\Shared\Application\DTO\AbstractDTO;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO pour l'ajout d'un nouveau Cost Item.
 * Conçu pour être utilisé avec le CostItemFormType.
 */
final class CreateCostItemDTO extends AbstractDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    public ?string $name;

    #[Assert\NotBlank]
    public ?string $type;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $targetAmount;

    #[Assert\NotBlank]
    public ?string $currency = 'EUR';

    #[Assert\NotBlank]
    #[Assert\Type(\DateTimeImmutable::class)]
    public ?\DateTimeImmutable $startDate;

    #[Assert\NotBlank]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Assert\GreaterThan(propertyPath: 'startDate')]
    public ?\DateTimeImmutable $endDate;

    public ?string $description;

    public ?string $userId;

    /**
     * Le constructeur peut être utilisé pour définir des valeurs par défaut intelligentes
     * afin d'améliorer l'expérience utilisateur dans le formulaire.
     */
    public function __construct()
    {
        // Pré-remplir les dates pour le mois en cours
        $this->startDate = new \DateTimeImmutable('first day of this month');
        $this->endDate = new \DateTimeImmutable('last day of this month');
    }
}
