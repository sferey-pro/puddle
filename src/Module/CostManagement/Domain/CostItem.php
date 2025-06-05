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
use App\Module\CostManagement\Domain\Exception\CostItemAlreadyArchivedException;
use App\Module\CostManagement\Domain\Exception\CostItemCannotBeArchivedException;
use App\Module\CostManagement\Domain\Exception\CostItemCannotBeReactivatedException;
use App\Module\CostManagement\Domain\Exception\CostItemCannotReceiveContributionException;
use App\Module\CostManagement\Domain\Exception\CostItemNotArchivedException;
use App\Module\CostManagement\Domain\Exception\InvalidContributionAmountException;
use App\Module\CostManagement\Domain\Exception\TargetAmountUpdateConflictException;
use App\Module\CostManagement\Domain\Specification\CostItemCanBeArchivedSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemCanBeReactivatedSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemCanReceiveContributionSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemIsAlreadyArchivedSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemIsFullyCoveredSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemTargetCanBeSafelyUpdatedSpecification;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\CostManagement\Domain\ValueObject\CostItemName;
use App\Module\CostManagement\Domain\ValueObject\CoveragePeriod;
use App\Module\SharedContext\Domain\ValueObject\Money;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Model\DomainEventTrait;

/**
 * Représente un poste de coût individuel.
 *
 * C'est une racine d'agrégat qui encapsule la logique métier pour gérer
 * les objectifs financiers, les contributions, et le cycle de vie d'un poste de coût
 * (actif, couvert, archivé).
 */
class CostItem extends AggregateRoot
{
    use DomainEventTrait;

    private CostItemName $name;
    private ?string $description;
    private Money $targetAmount;
    private Money $currentAmountCovered;
    private CoveragePeriod $coveragePeriod;
    private CostItemStatus $status;

    private function __construct(
        private readonly CostItemId $id,
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
     * Factory method pour créer un nouveau poste de coût.
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

    /**
     * Ajoute une contribution financière à ce poste de coût.
     *
     * @throws CostItemCannotReceiveContributionException Si le poste de coût ne peut pas recevoir de contribution.
     * @throws InvalidContributionAmountException Si le montant de la contribution est invalide.
     */
    public function addContribution(Money $contributionAmount): void
    {
        if (!(new CostItemCanReceiveContributionSpecification())->isSatisfiedBy($this)) {
            throw CostItemCannotReceiveContributionException::notActive($this->id);
        }

        if ($contributionAmount->getAmount() <= 0) {
            throw InvalidContributionAmountException::mustBePositive();
        }

        $this->currentAmountCovered = $this->currentAmountCovered->add($contributionAmount);

        $this->recordDomainEvent(new CostContributionReceived(
            $this->id,
            $contributionAmount,
            $this->currentAmountCovered
        ));

        if ((new CostItemIsFullyCoveredSpecification())->isSatisfiedBy($this)) {
            $this->markAsCovered();
        }
    }

    /**
     * Met à jour les détails du poste de coût.
     *
     * @throws TargetAmountUpdateConflictException Si le nouveau montant cible est inférieur au montant déjà couvert.
     * @throws \LogicException Si l'item n'est pas dans un état modifiable.
     */
    public function updateDetails(
        CostItemName $name,
        Money $targetAmount,
        CoveragePeriod $coveragePeriod,
        ?string $description
    ): void {
        if ($this->status->equals(CostItemStatus::ARCHIVED)) {
            throw new \LogicException('Les détails d\'un poste archivé ne peuvent être modifiés.');
        }

        if (!(new CostItemTargetCanBeSafelyUpdatedSpecification($targetAmount))->isSatisfiedBy($this)) {
            throw TargetAmountUpdateConflictException::newTargetBelowCurrent($targetAmount, $this->currentAmountCovered);
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
            $this->id,
            $this->name, $oldName,
            $this->targetAmount, $oldTargetAmount,
            $this->coveragePeriod, $oldCoveragePeriod,
            $this->description, $oldDescription
        ));

        if ($this->status === CostItemStatus::ACTIVE && (new CostItemIsFullyCoveredSpecification())->isSatisfiedBy($this)) {
            $this->markAsCovered();
        }
    }

    /**
     * Archive le poste de coût, le rendant inactif.
     *
     * @throws CostItemAlreadyArchivedException Si le poste est déjà archivé.
     * @throws CostItemCannotBeArchivedException Si les conditions pour l'archivage ne sont pas remplies.
     */
    public function archive(?\DateTimeImmutable $currentDate = null): void
    {
        if ((new CostItemIsAlreadyArchivedSpecification())->isSatisfiedBy($this)) {
            throw CostItemAlreadyArchivedException::withId($this->id);
        }

        $currentDate = $currentDate ?? new \DateTimeImmutable();
        if (!(new CostItemCanBeArchivedSpecification($currentDate))->isSatisfiedBy($this)) {
            throw CostItemCannotBeArchivedException::forId($this->id);
        }

        $this->status = CostItemStatus::ARCHIVED;
        $this->recordDomainEvent(new CostItemArchived($this->id));
    }

    /**
     * Réactive un poste de coût archivé.
     *
     * @throws CostItemNotArchivedException si le poste n'est pas actuellement archivé.
     * @throws CostItemCannotBeReactivatedException si les conditions pour la réactivation ne sont pas remplies (ex: période expirée).
     */
    public function reactivate(?\DateTimeImmutable $currentDate = null): void
    {
        $currentDate = $currentDate ?? new \DateTimeImmutable();

        // On vérifie toutes les conditions de réactivation en une fois.
        if (!(new CostItemCanBeReactivatedSpecification($currentDate))->isSatisfiedBy($this)) {
            // Si la spécification échoue, on vérifie la raison pour lancer une exception plus précise.
            if (!(new CostItemIsAlreadyArchivedSpecification())->isSatisfiedBy($this)) {
                throw CostItemNotArchivedException::withId($this->id);
            }

            // Si l'état était bien archivé, l'échec vient forcément de la période de couverture.
            throw CostItemCannotBeReactivatedException::coveragePeriodEnded($this->id);
        }

        // Détermination du nouveau statut après réactivation.
        $this->status = (new CostItemIsFullyCoveredSpecification())->isSatisfiedBy($this)
            ? CostItemStatus::FULLY_COVERED
            : CostItemStatus::ACTIVE;

        $this->recordDomainEvent(new CostItemReactivated(
            $this->id(),
            $this->status
        ));
    }

    /**
     * Marque le poste de coût comme étant entièrement couvert.
     */
    private function markAsCovered(): void
    {
        if ($this->status->equals(CostItemStatus::ACTIVE)) {
            $this->status = CostItemStatus::FULLY_COVERED;
            $this->recordDomainEvent(new CostItemCovered($this->id, $this->currentAmountCovered));
        }
    }
}
