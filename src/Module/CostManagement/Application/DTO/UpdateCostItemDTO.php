<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\DTO;

use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\Enum\CostItemType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO pour la mise à jour d'un Cost Item.
 * Reprend les champs de CreateCostItemDTO et ajoute l'ID de l'item à modifier.
 */
final class UpdateCostItemDTO
{
    #[Assert\NotBlank]
    public ?string $id = '';

    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    public ?string $name = '';

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [CostItemType::class, 'values'])]
    public ?string $type = '';

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?int $targetAmount = 0;

    #[Assert\NotBlank]
    public string $currency = 'EUR';

    #[Assert\NotBlank]
    #[Assert\Type(\DateTimeImmutable::class)]
    public \DateTimeImmutable $startDate;

    #[Assert\NotBlank]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Assert\GreaterThan(propertyPath: 'startDate')]
    public \DateTimeImmutable $endDate;

    public ?string $description = null;

    /**
     * Factory method pour créer le DTO à partir d'un agrégat CostItem.
     */
    public static function fromCostItem(CostItem $costItem): self
    {
        $dto = new self();
        $dto->id = (string) $costItem->id();
        $dto->name = (string) $costItem->name();
        $dto->targetAmount = $costItem->targetAmount()->getAmount();
        $dto->currency = $costItem->targetAmount()->getCurrency();
        $dto->startDate = $costItem->coveragePeriod()->getStartDate();
        $dto->endDate = $costItem->coveragePeriod()->getEndDate();
        $dto->description = $costItem->description();

        return $dto;
    }

    #[Assert\Callback]
    public function validate(): void
    {
        if (null !== $this->targetAmount && null === $this->currency) {
            throw new \InvalidArgumentException('Currency must be provided when targetAmount is updated.');
        }

        if ((null !== $this->startDate || null !== $this->endDate) && !(null !== $this->startDate && null !== $this->endDate)) {
            throw new \InvalidArgumentException('Both startDate and endDate must be provided to update the coverage period, or neither.');
        }
    }
}
