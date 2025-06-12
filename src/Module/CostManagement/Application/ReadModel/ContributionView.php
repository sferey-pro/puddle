<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\ReadModel;

use App\Module\CostManagement\Domain\CostContribution;

/**
 * Représentation simplifiée d'une contribution pour le Read Model.
 * Ce DTO n'est pas persisté directement mais embarqué dans CostItemInstanceView.
 */
class ContributionView
{
    public string $id;
    public float $amount;
    public string $currency;
    public \DateTimeImmutable $contributedAt;
    public ?string $sourceProductId = null;

    public function __construct(
        string $id,
        float $amount,
        string $currency,
        \DateTimeImmutable $contributedAt,
        ?string $sourceProductId,
    ) {
        $this->id = $id;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->contributedAt = $contributedAt;
        $this->sourceProductId = $sourceProductId;
    }

    /**
     * Factory pour créer une vue à partir d'un agrégat existant (pour la réconciliation).
     */
    public static function fromEntity(CostContribution $contribution): self
    {
        return new self(
            id: (string) $contribution->id(),
            amount: $contribution->amount()->toFloat(),
            currency: $contribution->amount()->getCurrency(),
            contributedAt: $contribution->contributedAt(),
            sourceProductId: $contribution->sourceProductId() ? (string) $contribution->sourceProductId() : null
        );
    }
}
