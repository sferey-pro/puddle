<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain;

use App\Module\CostManagement\Domain\Enum\CostItemStatus;
use App\Module\CostManagement\Domain\Event\CostContributionReceived;
use App\Module\CostManagement\Domain\Event\CostItemAdded;
use App\Module\CostManagement\Domain\Event\CostItemArchived;
use App\Module\CostManagement\Domain\Event\CostItemCovered;
use App\Module\CostManagement\Domain\Event\CostItemDetailsUpdated;
use App\Module\CostManagement\Domain\Event\CostItemReactivated;
use App\Module\CostManagement\Domain\Exception\CostItemException;
use App\Module\CostManagement\Domain\Specification\CostItemCanBeArchivedSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemCanBeReactivatedSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemCanReceiveContributionSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemIsActiveSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemIsAlreadyArchivedSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemIsFullyCoveredSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemTargetCanBeSafelyUpdatedSpecification;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\CostManagement\Domain\ValueObject\CostItemName;
use App\Module\CostManagement\Domain\ValueObject\CoveragePeriod;
use App\Module\SharedContext\Domain\Exception\InvalidMoneyException;
use App\Module\SharedContext\Domain\ValueObject\Money;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Model\DomainEventTrait;

/**
 * Représente un poste de coût individuel au sein du domaine.
 *
 * CostItem est l'Agrégat Root pour le contexte de la gestion des coûts. Il encapsule
 * toutes les informations et les règles métier liées à un poste de coût, comme son nom,
 * son montant cible, sa période de couverture, et son statut.
 *
 * En tant qu'agrégat, il est responsable de maintenir son état dans un état cohérent
 * à travers des transitions (méthodes) qui valident les invariants du domaine avant
 * d'appliquer des changements. Chaque changement d'état significatif est enregistré
 * sous forme d'événement de domaine.
 */
class CostItem extends AggregateRoot
{
    use DomainEventTrait;

    private CostItemName $name;
    private Money $targetAmount;
    private Money $currentAmountCovered;
    private CoveragePeriod $coveragePeriod;
    private CostItemStatus $status;
    private ?string $description;

    private function __construct(
        private CostItemId $id,
        CostItemName $name,
        Money $targetAmount,
        CoveragePeriod $coveragePeriod,
        ?string $description = null,
    ) {
        $this->name = $name;
        $this->targetAmount = $targetAmount;
        $this->coveragePeriod = $coveragePeriod;
        $this->description = $description;
        $this->currentAmountCovered = Money::zero($this->targetAmount->getCurrency());
        $this->status = CostItemStatus::ACTIVE;
    }

    /**
     * Crée un nouveau poste de coût.
     * C'est la factory method pour instancier un CostItem.
     */
    public static function create(
        CostItemId $id,
        CostItemName $name,
        Money $targetAmount,
        CoveragePeriod $coveragePeriod,
        ?string $description = null,
    ): self {
        $costItem = new self($id, $name, $targetAmount, $coveragePeriod, $description);

        $costItem->recordDomainEvent(new CostItemAdded(
            $costItem->id(),
            $costItem->name(),
            $costItem->targetAmount(),
            $costItem->coveragePeriod(),
            $costItem->status()
        ));

        return $costItem;
    }

    /**
     * Ajoute une contribution financière à ce poste de coût.
     *
     * @throws CostItemException
     */
    public function addContribution(Money $contributionAmount): void
    {
        if (!(new CostItemCanReceiveContributionSpecification())->isSatisfiedBy($this)) {
            throw CostItemException::cannotReceiveContributionBecauseStatusIs($this->id, $this->status);
        }

        if ($contributionAmount->getCurrency() !== $this->targetAmount->getCurrency()) {
            throw InvalidMoneyException::currencyMismatch($this->targetAmount->getCurrency(), $contributionAmount->getCurrency());
        }

        if ($contributionAmount->getAmount() <= 0) {
            throw InvalidMoneyException::amountMustBePositive();
        }

        $this->currentAmountCovered = $this->currentAmountCovered->add($contributionAmount);

        $this->recordDomainEvent(new CostContributionReceived(
            $this->id(),
            $contributionAmount,
            $this->currentAmountCovered
        ));

        if ((new CostItemIsFullyCoveredSpecification())->isSatisfiedBy($this)) {
            $this->markAsCovered();
        }
    }

    /**
     * Archive le poste de coût, le rendant inactif pour de nouvelles contributions.
     *
     * @throws CostItemException
     */
    public function archive(?\DateTimeImmutable $currentDate = new \DateTimeImmutable()): void
    {
        if ((new CostItemIsAlreadyArchivedSpecification())->isSatisfiedBy($this)) {
            throw CostItemException::alreadyArchived($this->id);
        }

        if (!(new CostItemCanBeArchivedSpecification($currentDate))->isSatisfiedBy($this)) {
            throw CostItemException::cannotBeArchived($this->id);
        }

        $this->status = CostItemStatus::ARCHIVED;
        $this->recordDomainEvent(new CostItemArchived($this->id()));
    }

    /**
     * Réactive un poste de coût précédemment archivé.
     *
     * @throws CostItemException
     */
    public function reactivate(?\DateTimeImmutable $currentDate = new \DateTimeImmutable()): void
    {
        if (!(new CostItemCanBeReactivatedSpecification($currentDate))->isSatisfiedBy($this)) {
            throw CostItemException::cannotBeReactivated($this->id);
        }

        // Le nouveau statut dépend de si l'item était déjà couvert ou non.
        $newStatus = (new CostItemIsFullyCoveredSpecification())->isSatisfiedBy($this)
            ? CostItemStatus::FULLY_COVERED
            : CostItemStatus::ACTIVE;

        $this->status = $newStatus;
        $this->recordDomainEvent(new CostItemReactivated($this->id(), $this->status));
    }

    /**
     * Met à jour les détails principaux du poste de coût.
     *
     * @throws CostItemException
     */
    public function updateDetails(
        CostItemName $name,
        Money $targetAmount,
        CoveragePeriod $coveragePeriod,
        ?string $description,
    ): void {
        if (!(new CostItemIsActiveSpecification())->isSatisfiedBy($this)) {
            throw CostItemException::detailsUpdateNotAllowed($this->id, $this->status);
        }

        if (!(new CostItemTargetCanBeSafelyUpdatedSpecification($targetAmount))->isSatisfiedBy($this)) {
            throw CostItemException::targetAmountConflict($targetAmount, $this->currentAmountCovered);
        }

        $oldName = $this->name;
        $oldTargetAmount = $this->targetAmount;
        $oldCoveragePeriod = $this->coveragePeriod;
        $oldDescription = $this->description;

        $this->name = $name;
        $this->targetAmount = $targetAmount;
        $this->coveragePeriod = $coveragePeriod;
        $this->description = $description;

        $this->recordDomainEvent(new CostItemDetailsUpdated(
            $this->id, $this->name, $oldName, $this->targetAmount, $oldTargetAmount,
            $this->coveragePeriod, $oldCoveragePeriod, $this->description, $oldDescription
        ));

        // Après mise à jour, l'item peut devenir couvert.
        if (CostItemStatus::ACTIVE === $this->status && (new CostItemIsFullyCoveredSpecification())->isSatisfiedBy($this)) {
            $this->markAsCovered();
        }
    }

    /**
     * Passe le statut à FULLY_COVERED et enregistre un événement.
     */
    private function markAsCovered(): void
    {
        if ((new CostItemIsActiveSpecification())->isSatisfiedBy($this)) {
            $this->status = CostItemStatus::FULLY_COVERED;
            $this->recordDomainEvent(new CostItemCovered($this->id, $this->currentAmountCovered));
        }
    }

    public function id(): CostItemId
    {
        return $this->id;
    }

    public function name(): CostItemName
    {
        return $this->name;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function targetAmount(): Money
    {
        return $this->targetAmount;
    }

    public function currentAmountCovered(): Money
    {
        return $this->currentAmountCovered;
    }

    public function coveragePeriod(): CoveragePeriod
    {
        return $this->coveragePeriod;
    }

    public function status(): CostItemStatus
    {
        return $this->status;
    }
}
