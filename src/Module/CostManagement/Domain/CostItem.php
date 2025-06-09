<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain;

use App\Module\CostManagement\Domain\Enum\CostItemStatus;
use App\Module\CostManagement\Domain\Enum\CostItemType;
use App\Module\CostManagement\Domain\Event\CostContributionCancelled;
use App\Module\CostManagement\Domain\Event\CostContributionReceived;
use App\Module\CostManagement\Domain\Event\CostContributionRemoved;
use App\Module\CostManagement\Domain\Event\CostItemAdded;
use App\Module\CostManagement\Domain\Event\CostItemArchived;
use App\Module\CostManagement\Domain\Event\CostItemCovered;
use App\Module\CostManagement\Domain\Event\CostItemDetailsUpdated;
use App\Module\CostManagement\Domain\Event\CostItemReactivated;
use App\Module\CostManagement\Domain\Exception\CostItemException;
use App\Module\CostManagement\Domain\Specification\Composite\CostItemCanBeArchivedSpecification;
use App\Module\CostManagement\Domain\Specification\Composite\CostItemCanBeReactivatedSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemCanReceiveContributionSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemIsActiveSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemIsArchivedSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemIsFullyCoveredSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemTargetCanBeSafelyUpdatedSpecification;
use App\Module\CostManagement\Domain\ValueObject\CostContributionId;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\CostManagement\Domain\ValueObject\CostItemName;
use App\Module\CostManagement\Domain\ValueObject\CoveragePeriod;
use App\Module\SharedContext\Domain\Exception\InvalidMoneyException;
use App\Module\SharedContext\Domain\ValueObject\Money;
use App\Module\SharedContext\Domain\ValueObject\ProductId;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Model\DomainEventTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
    private CoveragePeriod $coveragePeriod;
    private CostItemStatus $status;
    private ?string $description;
    private CostItemType $type;

    /**
     * @var Collection<int, CostContribution>
     */
    private Collection $contributions;

    private function __construct(
        private CostItemId $id,
        CostItemName $name,
        CostItemType $type,
        Money $targetAmount,
        CoveragePeriod $coveragePeriod,
        ?string $description = null,
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->targetAmount = $targetAmount;
        $this->coveragePeriod = $coveragePeriod;
        $this->description = $description;
        $this->status = CostItemStatus::ACTIVE;
        $this->contributions = new ArrayCollection();
    }

    /**
     * Crée un nouveau poste de coût.
     * C'est la factory method pour instancier un CostItem.
     */
    public static function create(
        CostItemName $name,
        CostItemType $type,
        Money $targetAmount,
        CoveragePeriod $coveragePeriod,
        ?string $description = null,
    ): self {
        $id = CostItemId::generate();

        $costItem = new self($id, $name, $type, $targetAmount, $coveragePeriod, $description);

        $costItem->recordDomainEvent(new CostItemAdded(
            $id,
            $costItem->name(),
            $costItem->type(),
            $costItem->targetAmount(),
            $costItem->coveragePeriod(),
            $costItem->status()
        ));

        return $costItem;
    }

    /**
     * Trouve une contribution par son ID au sein de la collection de l'agrégat.
     *
     * Cette méthode privée centralise la logique de recherche et la gestion des erreurs,
     * assurant que toute tentative d'accès à une contribution est sécurisée et cohérente.
     * Elle lève une exception si la contribution n'est pas trouvée.
     *
     * @throws CostItemException
     */
    private function findContributionOrFail(CostContributionId $contributionId): CostContribution
    {
        /** @var CostContribution|false $contribution */
        $contribution = $this->contributions
            ->filter(fn (CostContribution $c) => $c->id()->equals($contributionId))
            ->first();

        if (false === $contribution) {
            throw CostItemException::contributionNotFound($contributionId);
        }

        return $contribution;
    }

    /**
     * Ajoute une contribution financière à ce poste de coût.
     *
     * @throws CostItemException
     */
    public function addContribution(Money $amount, ?ProductId $sourceProductId = null): void
    {
        if (!(new CostItemCanReceiveContributionSpecification())->isSatisfiedBy($this)) {
            throw CostItemException::cannotReceiveContributionBecauseStatusIs($this->id(), $this->status);
        }

        if ($amount->getCurrency() !== $this->targetAmount->getCurrency()) {
            throw InvalidMoneyException::currencyMismatch($this->targetAmount->getCurrency(), $amount->getCurrency());
        }

        if ($amount->getAmount() <= 0) {
            throw InvalidMoneyException::amountMustBePositive();
        }

        $contribution = CostContribution::create($this, $amount, $sourceProductId);
        $this->contributions->add($contribution);

        $this->recordDomainEvent(new CostContributionReceived(
            $this->id(),
            $contribution->id(),
            $contribution->amount(),
            $this->currentAmountCovered(),
            $sourceProductId
        ));

        if ((new CostItemIsFullyCoveredSpecification())->isSatisfiedBy($this)) {
            $this->markAsCovered();
        }
    }

    /**
     * Supprime une contribution de ce poste de coût.
     */
    public function removeContribution(CostContributionId $contributionId): void
    {
        $contributionToRemove = $this->findContributionOrFail($contributionId);

        $this->contributions->removeElement($contributionToRemove);

        $this->recordDomainEvent(new CostContributionRemoved(
            $this->id(),
            $contributionToRemove->id(),
            $this->currentAmountCovered()
        ));
    }

    /**
     * Annule une contribution existante sur ce poste de coût.
     */
    public function cancelContribution(CostContributionId $contributionId): void
    {
        $contributionToCancel = $this->findContributionOrFail($contributionId);

        $contributionToCancel->cancel();

        $this->recordDomainEvent(new CostContributionCancelled(
            $this->id(),
            $contributionToCancel->id(),
            $this->currentAmountCovered()
        ));
    }

    /**
     * Archive le poste de coût, le rendant inactif pour de nouvelles contributions.
     *
     * @throws CostItemException
     */
    public function archive(): void
    {
        if ((new CostItemIsArchivedSpecification())->isSatisfiedBy($this)) {
            throw CostItemException::alreadyArchived($this->id());
        }

        if (!(new CostItemCanBeArchivedSpecification())->isSatisfiedBy($this)) {
            throw CostItemException::cannotBeArchived($this->id());
        }

        $this->status = CostItemStatus::ARCHIVED;
        $this->recordDomainEvent(new CostItemArchived($this->id()));
    }

    /**
     * Réactive un poste de coût précédemment archivé.
     *
     * @throws CostItemException
     */
    public function reactivate(): void
    {
        if (!(new CostItemCanBeReactivatedSpecification())->isSatisfiedBy($this)) {
            throw CostItemException::cannotBeReactivated($this->id());
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
            throw CostItemException::detailsUpdateNotAllowed($this->id(), $this->status);
        }

        if (!(new CostItemTargetCanBeSafelyUpdatedSpecification($targetAmount))->isSatisfiedBy($this)) {
            throw CostItemException::targetAmountConflict($targetAmount, $this->currentAmountCovered());
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
            $this->id(), $this->name, $oldName, $this->targetAmount, $oldTargetAmount,
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
            $this->recordDomainEvent(new CostItemCovered($this->id(), $this->currentAmountCovered()));
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

    public function type(): CostItemType
    {
        return $this->type;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function targetAmount(): Money
    {
        return $this->targetAmount;
    }

    /**
     * Calcule et retourne le montant total actuellement couvert par les contributions.
     */
    public function currentAmountCovered(): Money
    {
        $total = Money::zero();
        foreach ($this->contributions as $contribution) {
            if ($contribution->isActive()) {
                $total = $total->add($contribution->amount());
            }
        }

        return $total;
    }

    public function coveragePeriod(): CoveragePeriod
    {
        return $this->coveragePeriod;
    }

    public function status(): CostItemStatus
    {
        return $this->status;
    }

    public function contributions(): Collection
    {
        return $this->contributions;
    }
}
