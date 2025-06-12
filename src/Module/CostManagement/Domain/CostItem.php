<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain;

use App\Core\Specification\AndSpecification;
use App\Core\Specification\NotSpecification;
use App\Module\CostManagement\Domain\Enum\CostItemStatus;
use App\Module\CostManagement\Domain\Enum\CostItemType;
use App\Module\CostManagement\Domain\Event\CostContributionCancelled;
use App\Module\CostManagement\Domain\Event\CostContributionReceived;
use App\Module\CostManagement\Domain\Event\CostContributionRemoved;
use App\Module\CostManagement\Domain\Event\CostContributionUpdated;
use App\Module\CostManagement\Domain\Event\CostItemAdded;
use App\Module\CostManagement\Domain\Event\CostItemArchived;
use App\Module\CostManagement\Domain\Event\CostItemCovered;
use App\Module\CostManagement\Domain\Event\CostItemDetailsUpdated;
use App\Module\CostManagement\Domain\Event\CostItemReactivated;
use App\Module\CostManagement\Domain\Event\CostItemReopened;
use App\Module\CostManagement\Domain\Exception\CostItemException;
use App\Module\CostManagement\Domain\Specification\Composite\CostItemCanBeArchivedSpecification;
use App\Module\CostManagement\Domain\Specification\Composite\CostItemCanBeReactivatedSpecification;
use App\Module\CostManagement\Domain\Specification\Composite\CostItemIsActiveSpecification;
use App\Module\CostManagement\Domain\Specification\Composite\CostItemIsFullyCoveredSpecification;
use App\Module\CostManagement\Domain\Specification\Composite\ShouldBecomeActiveAgainSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemAmountIsSufficientSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemCanReceiveContributionSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemHasStatusSpecification;
use App\Module\CostManagement\Domain\Specification\CostItemIsArchivedSpecification;
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
use App\Shared\Domain\Service\ClockInterface;
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

    /**
     * @var bool Indique si ce poste de coût sert de modèle pour unee planification récurente.
     */
    private bool $isTemplate;

    private CostItemType $type;
    private Money $targetAmount;

    private ?CoveragePeriod $coveragePeriod;
    private ?string $description;

    private CostItemStatus $status;

    /** @var Collection<int, CostContribution>  */
    private Collection $contributions;


    private function __construct(
        private CostItemId $id,
        CostItemName $name,
        bool $isTemplate,
        CostItemType $type,
        Money $targetAmount,
        ?CoveragePeriod $coveragePeriod,
        ?string $description = null,
    ) {
        $this->name = $name;
        $this->isTemplate = $isTemplate;
        $this->type = $type;
        $this->targetAmount = $targetAmount;

        $this->coveragePeriod = $coveragePeriod;
        $this->description = $description;

        $this->status = $isTemplate ? CostItemStatus::TEMPLATE : CostItemStatus::ACTIVE;
        $this->contributions = new ArrayCollection();
    }

    /**
     * Crée un nouveau poste de coût.
     * C'est la factory method pour instancier un CostItem.
     */
    public static function create(
        CostItemName $name,
        bool $isTemplate,
        CostItemType $type,
        Money $targetAmount,
        ?CoveragePeriod $coveragePeriod,
        ?string $description = null,
    ): self {
        $id = CostItemId::generate();

        $costItem = new self(
            id : $id,
            isTemplate: $isTemplate,
            name: $name,
            type: $type,
            targetAmount: $targetAmount,
            coveragePeriod: $isTemplate ? null : $coveragePeriod,
            description: $description
        );

        $costItem->recordDomainEvent(new CostItemAdded(
            $id,
            $costItem->name(),
            $costItem->isTemplate(),
            $costItem->type(),
            $costItem->targetAmount(),
            $costItem->status(),
            $costItem->coveragePeriod()
        ));

        return $costItem;
    }

    /**
     * Crée une nouvelle instance de CostItem à partir du modèle actuel.
     *
     * @param ClockInterface $clock
     * @return self
     */
    public function createInstanceFromTemplate(ClockInterface $clock, string $durationModifier): self
    {
        if (!$this->isTemplate) {
            throw CostItemException::notTemplate($this->id);
        }

        $instanceName = sprintf(
            '%s - %s',
            $this->name,
            $clock->now()->format('F Y')
        );

        // On utilise la factory pour créer la nouvelle instance
        return self::create(
            new CostItemName($instanceName),
            false, // L'objet créé est une instance, pas un modèle
            $this->type,
            $this->targetAmount,
            CoveragePeriod::fromClock($clock, $durationModifier),
            $this->description
        );
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

        // On vérifie si la création d'une contribution modifie le status du poste de coût
        $this->updateStatusAfterContributionChange();
    }

    public function updateContribution(
        CostContributionId $contributionId,
        Money $newAmount,
        ?ProductId $newSourceProductId,
    ): void {
        $contribution = $this->findContributionOrFail($contributionId);

        // On ne peut modifier une contribution que si le poste de coût n'est pas archivé
        $canBeUpdatedSpec = new NotSpecification(new CostItemIsArchivedSpecification());

        if (!$canBeUpdatedSpec->isSatisfiedBy($this)) {
            throw CostItemException::detailsUpdateNotAllowed($this->id, $this->status());
        }

        // On appelle une méthode sur l'entité enfant pour qu'elle se mette à jour
        $contribution->update($newAmount, $newSourceProductId);

        // On enregistre un événement pour signaler le changement
        $this->recordDomainEvent(new CostContributionUpdated(
            $this->id,
            $contribution->id(),
            $contribution->amount(),
            $this->currentAmountCovered(),
            $contribution->sourceProductId()
        ));

        // On vérifie si le changement modifie le status du poste de coût
        $this->updateStatusAfterContributionChange();
    }

    /**
     * Met à jour le statut du CostItem après une modification de ses contributions.
     * Cette méthode est le point central de décision pour les changements de statut.
     */
    private function updateStatusAfterContributionChange(): void
    {
        // Règle 1: Si le statut est archivé, on ne change rien. C'est un état terminal.
        if ((new CostItemIsArchivedSpecification())->isSatisfiedBy($this)) {
            return;
        }

        // Règle 2: Définition de la condition pour passer à "Entièrement Couvert".
        // L'item doit avoir un montant suffisant ET ne doit pas déjà avoir le statut "FULLY_COVERED".
        $shouldBecomeFullyCoveredSpec = new AndSpecification(
            new CostItemAmountIsSufficientSpecification(),
            new NotSpecification(new CostItemHasStatusSpecification(CostItemStatus::FULLY_COVERED))
        );

        if ($shouldBecomeFullyCoveredSpec->isSatisfiedBy($this)) {
            $this->markAsCovered();

            return; // Le statut a changé, on arrête le traitement ici.
        }

        // Règle 3: Définition de la condition pour (re)passer à "Actif".
        // L'item devait avoir le statut "FULLY_COVERED", son montant n'est plus suffisant,
        // ET sa période de couverture est toujours active.
        // Règle pour (re)passer à "Actif"
        if ((new ShouldBecomeActiveAgainSpecification())->isSatisfiedBy($this)) {
            $this->status = CostItemStatus::ACTIVE;
            $this->recordDomainEvent(new CostItemReopened($this->id()));
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

        // Après mise à jour,
        // On vérifie si la modification modifie le status du poste de coût
        $this->updateStatusAfterContributionChange();
    }

    /**
     * Passe le statut à FULLY_COVERED et enregistre un événement.
     */
    private function markAsCovered(): void
    {
        $this->status = CostItemStatus::FULLY_COVERED;
        $this->recordDomainEvent(new CostItemCovered($this->id(), $this->currentAmountCovered()));
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

    public function coveragePeriod(): ?CoveragePeriod
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

    public function isTemplate(): bool
    {
        return $this->isTemplate;
    }
}
