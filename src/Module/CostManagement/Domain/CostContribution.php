<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain;

use App\Core\Application\Clock\SystemTime;
use App\Module\CostManagement\Domain\Enum\ContributionStatus;
use App\Module\CostManagement\Domain\ValueObject\CostContributionId;
use App\Module\SharedContext\Domain\ValueObject\Money;
use App\Module\SharedContext\Domain\ValueObject\ProductId;

/**
 * Représente une contribution financière unique à un poste de coût (CostItem).
 *
 * Chaque instance de cette classe est une transaction ou une imputation de coût
 * qui vient réduire le montant restant à couvrir pour un CostItem. Elle contient
 * son propre identifiant, le montant, la date, et une référence optionnelle
 * à l'origine de la contribution (ex: un produit vendu).
 * C'est une entité enfant de l'agrégat CostItem.
 */
class CostContribution
{
    /**
     * @var costItem L'agrégat parent
     */
    private CostItem $costItem;

    /**
     * @var Money le montant de la contribution
     */
    private Money $amount;

    /**
     * @var \DateTimeImmutable la date de création de la contribution
     */
    private \DateTimeImmutable $contributedAt;

    /**
     * @var ProductId|null référence optionnelle au produit qui a généré ce coût/cette contribution
     */
    private ?ProductId $sourceProductId;

    /**
     * @var ContributionStatus Le statut de la contribution (active, annulée, etc.).
     */
    private ContributionStatus $status;

    /**
     * Le constructeur est privé pour forcer l'utilisation de la factory method `create()`.
     * Cela garantit que chaque nouvelle contribution est correctement initialisée et que
     * la logique de création est centralisée.
     */
    private function __construct(
        /**
         * @var CostContributionId L'identifiant unique de cette contribution
         */
        private CostContributionId $id,
        CostItem $costItem,
        Money $amount,
        ?ProductId $sourceProductId = null,
    ) {
        $this->costItem = $costItem;
        $this->amount = $amount;
        $this->sourceProductId = $sourceProductId;
        $this->contributedAt = SystemTime::now();
        $this->status = ContributionStatus::ACTIVE;
    }

    /**
     * Factory method pour créer une nouvelle instance de CostContribution.
     * C'est le point d'entrée contrôlé pour la création de contributions.
     *
     * @param costItem       $costItem        L'agrégat parent
     * @param Money          $amount          le montant de la contribution
     * @param ProductId|null $sourceProductId L'origine optionnelle de la contribution
     *
     * @return self la nouvelle instance de la contribution
     */
    public static function create(
        CostItem $costItem,
        Money $amount,
        ?ProductId $sourceProductId = null,
    ): self {
        $id = CostContributionId::generate();

        return new self($id, $costItem, $amount, $sourceProductId);
    }

    public function update(Money $newAmount, ?ProductId $newSourceProductId): void
    {
        if ($this->amount->getCurrency() !== $newAmount->getCurrency()) {
            throw new \InvalidArgumentException('Cannot change currency of a contribution.');
        }

        $this->amount = $newAmount;
        $this->sourceProductId = $newSourceProductId;
    }

    /**
     * Annule la contribution.
     *
     * Cette méthode implémente la logique métier pour l'annulation :
     *  - elle change le statut de la contribution à 'CANCELLED'.
     */
    public function cancel(): void
    {
        if (!$this->isActive()) {
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
