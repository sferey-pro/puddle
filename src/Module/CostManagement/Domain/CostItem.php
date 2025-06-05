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
use App\Module\CostManagement\Domain\Specification\CostItemCanBeArchivedSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemCanBeReactivatedSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemCanReceiveContributionSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemIsActiveSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemIsFullyCoveredSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemTargetCanBeSafelyUpdatedSpecification;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\CostManagement\Domain\ValueObject\CostItemName;
use App\Module\CostManagement\Domain\ValueObject\CoveragePeriod;
use App\Module\SharedContext\Domain\ValueObject\Money;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Model\DomainEventTrait;
use Webmozart\Assert\Assert;

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
        private CostItemId $id,
        CostItemName $name,
        Money $targetAmount,
        CoveragePeriod $coveragePeriod,
        ?string $description = null,
    ) {
        Assert::true($targetAmount->getAmount() >= 0, 'Target amount cannot be negative.');

        $this->name = $name;
        $this->targetAmount = $targetAmount;
        $this->coveragePeriod = $coveragePeriod;
        $this->description = $description;
        $this->currentAmountCovered = Money::zero($this->targetAmount->getCurrency());
        $this->status = CostItemStatus::ACTIVE;
    }

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

    public function addContribution(Money $contributionAmount): void
    {
        $spec = new CostItemCanReceiveContributionSpecification();
        if (!$spec->isSatisfiedBy($this)) {
            throw new \LogicException('Cannot add contribution to this cost item at its current state.');
        }

        Assert::eq($contributionAmount->getCurrency(), $this->targetAmount->getCurrency(), 'Contribution currency must match target currency.');
        Assert::true($contributionAmount->getAmount() > 0, 'Contribution amount must be positive.');

        $this->currentAmountCovered = $this->currentAmountCovered->add($contributionAmount);

        $this->recordDomainEvent(new CostContributionReceived(
            $this->id(),
            $contributionAmount,
            $this->currentAmountCovered()
        ));

        $isCoveredSpec = new CostItemIsFullyCoveredSpecification();
        if ($isCoveredSpec->isSatisfiedBy($this)) {
            $this->markAsCovered();
        }
    }

    private function markAsCovered(): void
    {
        $isActiveSpec = new CostItemIsActiveSpecification();
        if ($isActiveSpec->isSatisfiedBy($this)) {
            $this->status = CostItemStatus::FULLY_COVERED;
            $this->recordDomainEvent(new CostItemCovered(
                $this->id(),
                $this->currentAmountCovered()
            ));
        }
    }

    public function archive(?\DateTimeImmutable $currentDate = null): void
    {
        $currentDate = $currentDate ?? new \DateTimeImmutable();
        $spec = new CostItemCanBeArchivedSpecification($currentDate);

        if (!$spec->isSatisfiedBy($this)) {
            throw new \LogicException('Cost item cannot be archived at this time based on its state or period.');
        }

        if (CostItemStatus::ARCHIVED !== $this->status) {
            $this->status = CostItemStatus::ARCHIVED;
            $this->recordDomainEvent(new CostItemArchived(
                $this->id()
            ));
        }
    }

    public function reactivate(?\DateTimeImmutable $currentDate = null): void
    {
        $currentDate = $currentDate ?? new \DateTimeImmutable();
        // Vous devrez créer cette Spécification
        $spec = new CostItemCanBeReactivatedSpecification($currentDate);

        if (!$spec->isSatisfiedBy($this)) {
            throw new \LogicException('Cost item cannot be reactivated at this time.');
        }

        // Déterminer le nouveau statut après réactivation
        $isCoveredSpec = new CostItemIsFullyCoveredSpecification();
        $newStatus = $isCoveredSpec->isSatisfiedBy($this) ? CostItemStatus::FULLY_COVERED : CostItemStatus::ACTIVE;

        $this->status = $newStatus;
        $this->recordDomainEvent(new CostItemReactivated(
            $this->id(),
            $this->status
        ));
    }

    public function updateDetails(
        CostItemName $name,
        Money $targetAmount,
        CoveragePeriod $coveragePeriod,
        ?string $description,
        // \DateTimeImmutable $currentDate = null // Optionnel si des règles de MàJ dépendent du temps
    ): void {
        // Règle 1: L'item doit être modifiable (ex: actif)
        $canBeUpdatedSpec = new CostItemIsActiveSpecification(); // Ou une spec plus spécifique "CanUpdateDetails"
        if (!$canBeUpdatedSpec->isSatisfiedBy($this)) {
            throw new \LogicException('Only active cost items can have their details updated.');
        }

        // Règle 2: Le nouveau montant cible est valide
        $targetUpdateSpec = new CostItemTargetCanBeSafelyUpdatedSpecification($targetAmount);
        if (!$targetUpdateSpec->isSatisfiedBy($this)) { // On passe $this qui a currentAmountCovered
            throw new \LogicException('New target amount cannot be less than the currently covered amount or other validation failed.');
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
            $this->id(),
            $this->name,
            $oldName,
            $this->targetAmount,
            $oldTargetAmount,
            $this->coveragePeriod,
            $oldCoveragePeriod,
            $this->description,
            $oldDescription
        ));

        // Après une mise à jour, vérifier si l'item devient couvert si son statut était actif
        if (CostItemStatus::ACTIVE === $this->status) {
            $isCoveredSpec = new CostItemIsFullyCoveredSpecification();
            if ($isCoveredSpec->isSatisfiedBy($this)) {
                $this->markAsCovered();
            }
        }
    }
}
