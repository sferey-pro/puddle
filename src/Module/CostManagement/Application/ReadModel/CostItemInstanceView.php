<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\ReadModel;

use App\Core\Application\Clock\SystemTime;
use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\Event\CostContributionReceived;
use App\Module\CostManagement\Domain\Event\CostContributionRemoved;
use App\Module\CostManagement\Domain\Event\CostContributionUpdated;
use App\Module\CostManagement\Domain\Event\CostItemAdded;
use App\Module\CostManagement\Domain\Event\CostItemDetailsUpdated;
use App\Module\SharedContext\Domain\ValueObject\Money;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Read model représentant un CostItem pour l'affichage.
 * Cette classe est intentionnellement mutable et contient la logique pour se construire et se mettre à jour
 * à partir des événements de domaine, la rendant autonome.
 */
class CostItemInstanceView
{
    public string $id;
    public string $name;
    public bool $isTemplate;
    public ?string $type = null;
    public float $targetAmount = 0.0;
    public float $currentAmount = 0.0;
    public string $currency = 'EUR';
    public string $startDate;
    public string $endDate;
    public string $status;

    // Champs calculés pour l'affichage
    public float $progressPercentage;
    public bool $isCovered;
    public bool $isActiveNow;

    /**
     * @var Collection<int, ContributionView>
     */
    public Collection $contributions;

    /**
     * Le constructeur est privé pour forcer la création via le factory `fromCostItemAdded`.
     */
    private function __construct(
        string $id,
        string $name,
        bool $isTemplate,
        ?string $type,
        float $targetAmount,
        float $currentAmount,
        string $currency,
        string $startDate,
        string $endDate,
        string $status,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->isTemplate = $isTemplate;
        $this->type = $type;
        $this->targetAmount = $targetAmount;
        $this->currentAmount = $currentAmount;
        $this->currency = $currency;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;

        $this->contributions = new ArrayCollection();
        $this->updateCalculatedFields();
    }

    /**
     * Factory pour créer une nouvelle vue à partir de l'événement de création.
     */
    public static function fromCostItemAdded(CostItemAdded $event): self
    {
        return new self(
            id: (string) $event->costItemId(),
            name: (string) $event->name(),
            isTemplate: $event->isTemplate(),
            type: $event->type()->value,
            targetAmount: self::convertMoneyToFloat($event->targetAmount()),
            currentAmount: 0.0,
            currency: $event->targetAmount()->currency,
            startDate: $event->coveragePeriod()->startDate()->format('Y-m-d'),
            endDate: $event->coveragePeriod()->endDate()->format('Y-m-d'),
            status: $event->status()->value,
        );
    }

    /**
     * Factory pour créer une vue à partir d'un agrégat existant (pour la réconciliation).
     */
    public static function fromAggregate(CostItem $item): self
    {
        $view = new self(
            id: (string) $item->id(),
            name: (string) $item->name(),
            isTemplate: $item->isTemplate(),
            type: $item->type()->value,
            targetAmount: self::convertMoneyToFloat($item->targetAmount()),
            currentAmount: self::convertMoneyToFloat($item->currentAmountCovered()),
            currency: $item->targetAmount()->currency(),
            startDate: $item->coveragePeriod()->startDate()->format('Y-m-d'),
            endDate: $item->coveragePeriod()->endDate()->format('Y-m-d'),
            status: $item->status()->value
        );

        // On synchronise aussi les contributions
        foreach ($item->contributions() as $contribution) {
            $view->contributions->add(ContributionView::fromEntity($contribution));
        }

        $view->updateCalculatedFields();

        return $view;
    }

    /**
     * Met à jour la vue à partir de l'état actuel de l'agrégat.
     */
    public function updateFromAggregate(CostItem $item): void
    {
        $this->name = (string) $item->name();
        $this->type = $item->type()->value;
        $this->targetAmount = self::convertMoneyToFloat($item->targetAmount());
        $this->currentAmount = self::convertMoneyToFloat($item->currentAmountCovered());
        $this->currency = $item->targetAmount()->currency();
        $this->startDate = $item->coveragePeriod()->startDate()->format('Y-m-d');
        $this->endDate = $item->coveragePeriod()->endDate()->format('Y-m-d');
        $this->status = $item->status()->value;

        // Logique de synchronisation des contributions (plus complexe, exemple simple ici)
        // Pour une version robuste, il faudrait comparer chaque contribution
        $this->contributions->clear();
        foreach ($item->contributions() as $contribution) {
            $this->contributions->add(ContributionView::fromEntity($contribution));
        }

        $this->updateCalculatedFields();
    }

    /**
     * Compare la vue à l'agrégat pour détecter les différences.
     * Retourne true si une différence est trouvée.
     */
    public function isDifferentFrom(CostItem $item): bool
    {
        if ($this->name !== (string) $item->name()) {
            return true;
        }
        if ($this->status !== $item->status()->value) {
            return true;
        }

        // Comparaison des floats avec une petite tolérance
        if (abs($this->targetAmount - $item->targetAmount()->toFloat()) > 0.001) {
            return true;
        }
        if (abs($this->currentAmount - $item->currentAmountCovered()->toFloat()) > 0.001) {
            return true;
        }

        // Comparaison du nombre de contributions
        if ($this->contributions->count() !== \count($item->contributions())) {
            return true;
        }

        return false;
    }

    /**
     * Applique les changements de l'événement de mise à jour des détails.
     */
    public function updateFromDetails(CostItemDetailsUpdated $event): void
    {
        $this->name = (string) $event->newName();
        $this->targetAmount = self::convertMoneyToFloat($event->newTargetAmount());
        $this->startDate = $event->newCoveragePeriod()->startDate()->format('Y-m-d');
        $this->endDate = $event->newCoveragePeriod()->endDate()->format('Y-m-d');
        $this->updateCalculatedFields();
    }

    /**
     * Applique l'événement de réception d'une contribution pour mettre à jour la vue.
     */
    public function applyCostContributionReceived(CostContributionReceived $event): void
    {
        // Crée la vue de la contribution à partir de l'événement
        $contributionView = new ContributionView(
            id: (string) $event->costContributionId(),
            amount: $event->contributionAmount()->toFloat(),
            currency: $event->contributionAmount()->currency(),
            contributedAt: $event->occurredOn,
            sourceProductId: $event->sourceProductId() ? (string) $event->sourceProductId() : null
        );

        // Ajoute à la collection
        $this->contributions->add($contributionView);

        // Met à jour le montant total et recalcule les champs dérivés
        $this->currentAmount = $event->newTotalCoveredAmount()->toFloat();
        $this->updateCalculatedFields();
    }

    /**
     * Applique les changements de l'événement de mise à jour de contribution.
     * Trouve la contribution existante dans la collection et met à jour ses champs.
     */
    public function applyCostContributionUpdated(CostContributionUpdated $event): void
    {
        // On parcourt la collection de contributions
        $contributionViewToUpdate = $this->findContributionViewById((string) $event->costContributionId());

        if ($contributionViewToUpdate) {
            // On met à jour l'objet ContributionView trouvé
            $contributionViewToUpdate->amount = $event->newContributionAmount()->toFloat();
            $contributionViewToUpdate->sourceProductId = $event->newSourceProductId() ? (string) $event->newSourceProductId() : null;
        }

        // On met à jour le montant total couvert de l'ensemble du CostItem
        $this->currentAmount = $event->newTotalCoveredAmount()->toFloat();
        $this->updateCalculatedFields();
    }

    /**
     * Applique l'événement de suppression d'une contribution.
     */
    public function applyCostContributionRemoved(CostContributionRemoved $event): void
    {
        // Filtre la collection pour retirer la contribution
        $this->contributions = $this->contributions->filter(
            fn (ContributionView $c) => $c->id !== (string) $event->costContributionId()
        );

        // Recalcule le montant total couvert à partir des contributions restantes
        $this->recalculateCurrentAmount();
        $this->updateCalculatedFields();
    }

    /**
     * Met à jour le statut de la vue.
     */
    public function updateStatus(string $newStatus): void
    {
        $this->status = $newStatus;
        $this->updateCalculatedFields();
    }

    /**
     * Met à jour tous les champs calculés du Read Model.
     * Cette méthode est appelée après chaque modification pour garantir la cohérence.
     */
    private function updateCalculatedFields(): void
    {
        $this->recalculateProgressPercentage();
        $this->recalculateIsCovered();
        $this->recalculateIsActiveNow();
    }

    /**
     * Recalcule le montant couvert en se basant sur les contributions actuelles.
     * Utile principalement après la suppression d'une contribution.
     */
    private function recalculateCurrentAmount(): void
    {
        $total = 0.0;
        foreach ($this->contributions as $contribution) {
            $total += $contribution->amount;
        }
        $this->currentAmount = $total;
    }

    /**
     * Calcule le pourcentage de progression vers l'objectif.
     */
    private function recalculateProgressPercentage(): void
    {
        if ($this->targetAmount > 0) {
            $this->progressPercentage = min(100.0, ($this->currentAmount / $this->targetAmount) * 100);
        } else {
            $this->progressPercentage = $this->currentAmount > 0 ? 100.0 : 0.0;
        }
    }

    /**
     * Détermine si le coût est entièrement couvert.
     */
    private function recalculateIsCovered(): void
    {
        $this->isCovered = $this->targetAmount > 0 && $this->currentAmount >= $this->targetAmount;
    }

    /**
     * Détermine si la période de couverture est actuellement active.
     */
    private function recalculateIsActiveNow(): void
    {
        try {
            $currentDate = SystemTime::now();
            $startDate = new \DateTimeImmutable($this->startDate);
            $endDate = new \DateTimeImmutable($this->endDate);

            $this->isActiveNow = $currentDate >= $startDate && $currentDate <= $endDate;
        } catch (\Exception $e) {
            $this->isActiveNow = false;
        }
    }

    /**
     * Utilitaire pour convertir un objet Money en float.
     */
    private static function convertMoneyToFloat(Money $money): float
    {
        return $money->toFloat();
    }

    /**
     * Recherche et retourne une ContributionView à partir de son ID dans la collection.
     *
     * @param string $id L'identifiant de la contribution à trouver
     *
     * @return ContributionView|null la vue de la contribution ou null si elle n'est pas trouvée
     */
    private function findContributionViewById(string $id): ?ContributionView
    {
        foreach ($this->contributions as $contribution) {
            if ($contribution->id === $id) {
                return $contribution;
            }
        }

        return null;
    }
}
