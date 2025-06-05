<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * DTO pour l'ajout d'un nouveau Cost Item.
 * Conçu pour être utilisé avec le CostItemFormType.
 */
final class AddCostItemDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    public ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $targetAmount = null;

    #[Assert\NotBlank]
    public string $currency = 'EUR';

    #[Assert\NotBlank]
    #[Assert\Type(\DateTimeImmutable::class)]
    public ?\DateTimeImmutable $startDate = null;

    #[Assert\NotBlank]
    #[Assert\Type(\DateTimeImmutable::class)]
    public ?\DateTimeImmutable $endDate = null;

    public ?string $userId = null;

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

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context, $payload): void
    {
        if ($this->startDate && $this->endDate && $this->endDate < $this->startDate) {
            $context->buildViolation('La date de fin ne peut pas être antérieure à la date de début.')
                ->atPath('endDate') // Associe l'erreur au champ 'endDate' dans le formulaire
                ->addViolation();
        }
    }
}
