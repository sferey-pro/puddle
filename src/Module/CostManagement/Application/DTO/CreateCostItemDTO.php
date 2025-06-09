<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\DTO;

use App\Module\CostManagement\Domain\Enum\CostItemType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Sequentially;

/**
 * DTO pour l'ajout d'un nouveau Cost Item.
 * Conçu pour être utilisé avec le CostItemFormType.
 */
final class CreateCostItemDTO
{
    #[Sequentially(
        constraints: [
            new Assert\NotBlank(),
            new Assert\Length(min: 3),
        ]
    )]
    public string $name = '';

    #[Sequentially(
        constraints: [
            new Assert\NotBlank(),
            new Assert\Choice(callback: [CostItemType::class, 'values']),
        ]
    )]
    public ?string $type = '';

    #[Sequentially(
        constraints: [
            new Assert\NotBlank(),
            new Assert\PositiveOrZero(),
        ]
    )]
    public int $targetAmount = 0;

    #[Assert\NotBlank]
    public string $currency = 'EUR';

    #[Sequentially(
        constraints: [
            new Assert\NotBlank(),
            new Assert\Type(\DateTimeImmutable::class),
        ]
    )]
    public \DateTimeImmutable $startDate;

    #[Sequentially(
        constraints: [
            new Assert\NotBlank(),
            new Assert\Type(\DateTimeImmutable::class),
            new Assert\GreaterThan(propertyPath: 'startDate'),
        ]
    )]
    public \DateTimeImmutable $endDate;

    public ?string $description = null;

    public ?string $userId = null;

    public function __construct()
    {
        // Pré-remplir les dates pour le mois en cours
        $this->startDate = new \DateTimeImmutable('first day of this month');
        $this->endDate = new \DateTimeImmutable('last day of this month');
    }
}
