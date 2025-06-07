<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\ReadModel;

use App\Module\CostManagement\Domain\Event\CostContributionCancelled;
use App\Module\CostManagement\Domain\Event\CostContributionReceived;
use App\Module\CostManagement\Domain\Event\CostItemAdded;
use App\Module\CostManagement\Domain\Event\CostItemDetailsUpdated;
use App\Module\SharedContext\Domain\ValueObject\Money;
use App\Shared\Domain\Service\SystemTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Read model représentant un CostItem pour l'affichage.
 * Cette classe est intentionnellement mutable et contient la logique pour se construire et se mettre à jour
 * à partir des événements de domaine, la rendant autonome.
 */
class CostItemView
{
    public string $id;
    public string $name;
    public ?string $type = null;
    public float $targetAmount = 0.0;
    public float $currentAmount = 0.0;
    public string $currency = 'EUR';
    public string $startDate;
    public string $endDate;
    public string $status;

    public float $progressPercentage;
    public bool $isCovered;
    public bool $isActiveNow;

    /**
     * @var Collection<int, ContributionView>
     */
    public Collection $contributions;

    private function __construct(
        string $id,
        string $name,
        ?string $type,
        float $targetAmount,
        float $currentAmount,
        string $currency,
        string $startDate,
        string $endDate,
        string $status
    ) {
        $this->id = $id;
        $this->name = $name;
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

    public static function fromCostItemAdded(CostItemAdded $event): self
    {
        $view = new self(
            id: (string) $event->costItemId(),
            name: (string) $event->name(),
            type: $event->type()->value,
            targetAmount: self::convertMoneyToFloat($event->targetAmount()),
            currentAmount: 0.0,
            currency: $event->targetAmount()->getCurrency(),
            startDate: $event->coveragePeriod()->getStartDate()->format('Y-m-d'),
            endDate: $event->coveragePeriod()->getEndDate()->format('Y-m-d'),
            status: $event->status()->value,
        );

        $view->updateCalculatedFields();

        return $view;
    }

    public function updateFromDetails(CostItemDetailsUpdated $event): void
    {
        $this->name = (string) $event->newName();
        $this->targetAmount = self::convertMoneyToFloat($event->newTargetAmount());
        $this->startDate = $event->newCoveragePeriod()->getStartDate()->format('Y-m-d');
        $this->endDate = $event->newCoveragePeriod()->getEndDate()->format('Y-m-d');
        $this->updateCalculatedFields();
    }

    /**
     * Met à jour la vue lors de la réception d'une nouvelle contribution.
     */
    public function addContribution(CostContributionReceived $event): void
    {
        // Crée la vue de la contribution
        $contributionView = new ContributionView(
            id: (string) $event->contributionId,
            amount: $event->amount->toFloat(),
            currency: $event->amount->getCurrency(),
            contributedAt: $event->occurredOn,
            sourceProductId: $event->sourceProductId ? (string) $event->sourceProductId : null
        );

        // Ajoute à la collection
        $this->contributions->add($contributionView);

        // Met à jour le montant total et les champs calculés
        $this->currentAmount = self::convertMoneyToFloat($event->newTotalCovered);
        $this->updateCalculatedFields();
    }

    /**
     * Met à jour la vue lors de l'annulation d'une contribution.
     */
    public function cancelContribution(CostContributionCancelled $event): void
    {
        // On cherche la contribution à annuler/modifier dans notre vue
        $contributionToCancel = $this->contributions->findFirst(
            fn(int $key, ContributionView $view) => $view->id === (string) $event->contributionId
        );

        if ($contributionToCancel) {
            // Ici, on pourrait ajouter un statut "cancelled" à ContributionView et le mettre à jour.
            // Pour l'instant, et pour garder la simplicité, nous allons la supprimer de la vue.
            $this->contributions->removeElement($contributionToCancel);
        }

        // Met à jour le montant total et les champs calculés
        $this->currentAmount = self::convertMoneyToFloat($event->newTotalCovered);
        $this->updateCalculatedFields();
    }

    public function updateStatus(string $newStatus): void
    {
        $this->status = $newStatus;
        $this->updateCalculatedFields();
    }

    /**
     * Met à jour tous les champs calculés du Read Model.
     */
    private function updateCalculatedFields(): void
    {
        $this->recalculateProgressPercentage();
        $this->recalculateIsCovered();
        $this->recalculateIsActiveNow();
    }

    private function recalculateProgressPercentage(): void
    {
        if ($this->targetAmount > 0) {
            $this->progressPercentage = min(100.0, ($this->currentAmount / $this->targetAmount) * 100);
        } else {
            $this->progressPercentage = $this->currentAmount > 0 ? 100.0 : 0.0;
        }
    }

    private function recalculateIsCovered(): void
    {
        $this->isCovered = $this->currentAmount >= $this->targetAmount;
    }

    private function recalculateIsActiveNow(): void
    {
        $currentDate = SystemTime::now();
        $startDate = new \DateTimeImmutable($this->startDate);
        $endDate = new \DateTimeImmutable($this->endDate);

        $this->isActiveNow = $currentDate >= $startDate && $currentDate <= $endDate;
    }

    private static function convertMoneyToFloat(Money $money): float
    {
        return $money->toFloat();
    }
}
