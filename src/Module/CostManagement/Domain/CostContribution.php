<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain;

use App\Module\CostManagement\Domain\Enum\ContributionStatus;
use App\Module\CostManagement\Domain\ValueObject\CostContributionId;
use App\Module\ProductCatalog\Domain\ValueObject\ProductId;
use App\Module\SharedContext\Domain\ValueObject\Money;

/**
 * Représente une contribution financière unique à un poste de coût (CostItem).
 *
 * Chaque instance de cette classe est une transaction ou une imputation de coût
 * qui vient réduire le montant restant à couvrir pour un CostItem. Elle contient
 * son propre identifiant, le montant, la date, et une référence optionnelle
 * à l'origine de la contribution (ex: un produit vendu).
 */
class CostContribution
{
    private CostItem $costItem;
    private Money $amount;
    private \DateTimeImmutable $contributedAt;
    private ?ProductId $sourceProductId;
    private ContributionStatus $status;

    /**
     * Le constructeur est privé pour forcer l'utilisation de la factory method `create()`.
     * Cela garantit que chaque nouvelle contribution est correctement initialisée.
     */
    private function __construct(
        private CostContributionId $id,
        CostItem $costItem,
        Money $amount,
        ?ProductId $sourceProductId = null,
    ) {
        $this->costItem = $costItem;
        $this->amount = $amount;
        $this->sourceProductId = $sourceProductId;
        $this->contributedAt = new \DateTimeImmutable();
        $this->status = ContributionStatus::ACTIVE;
    }

    /**
     * Crée une nouvelle contribution.
     */
    public static function create(
        CostItem $costItem,
        Money $amount,
        ?ProductId $sourceProductId = null,
    ): self {
        $id = CostContributionId::generate();

        return new self($id, $costItem, $amount, $sourceProductId);
    }

    /**
     * Annule la contribution.
     *
     * Cette méthode implémente la logique métier pour l'annulation :
     * elle change le statut de la contribution à 'CANCELLED'.
     * Une garde empêche d'annuler une contribution déjà annulée.
     */
    public function cancel(): void
    {
        if (!$this->isActive()) {
            // Déjà annulée, on ne fait rien pour rester idempotent.
            return;
        }

        $this->status = ContributionStatus::CANCELLED;
    }

    public function isActive(): bool
    {
        return ContributionStatus::ACTIVE === $this->status;
    }

    public function id(): CostContributionId
    {
        return $this->id;
    }

    public function costItem(): CostItem
    {
        return $this->costItem;
    }

    public function amount(): Money
    {
        return $this->amount;
    }

    public function contributedAt(): \DateTimeImmutable
    {
        return $this->contributedAt;
    }

    public function sourceProductId(): ?ProductId
    {
        return $this->sourceProductId;
    }
}
