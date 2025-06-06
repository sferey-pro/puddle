<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\ReadModel;

use App\Module\CostManagement\Domain\Event\CostContributionReceived;
use App\Module\CostManagement\Domain\Event\CostItemAdded;
use App\Module\CostManagement\Domain\Event\CostItemDetailsUpdated;
use App\Module\SharedContext\Domain\ValueObject\Money;

/**
 * Read model représentant un CostItem pour l'affichage.
 * Cette classe est intentionnellement mutable et contient la logique pour se construire et se mettre à jour
 * à partir des événements de domaine, la rendant autonome.
 */
class CostItemView
{
    public float $progressPercentage;
    public bool $isCovered;
    public bool $isActiveNow;

    private function __construct(
        public string $id,
        public string $name,
        public float $targetAmount,
        public float $currentAmount,
        public string $currency,
        public string $startDate,
        public string $endDate,
        public string $status,
    ) {
    }

    public static function fromCostItemAdded(CostItemAdded $event): self
    {
        return new self(
            id: (string) $event->costItemId(),
            name: (string) $event->name(),
            targetAmount: self::convertMoneyToFloat($event->targetAmount()),
            currentAmount: 0.0,
            currency: $event->targetAmount()->getCurrency(),
            startDate: $event->coveragePeriod()->getStartDate()->format('Y-m-d'),
            endDate: $event->coveragePeriod()->getEndDate()->format('Y-m-d'),
            status: $event->status()->value,
        );
    }

    public function updateFromDetails(CostItemDetailsUpdated $event): void
    {
        $this->name = (string) $event->newName();
        $this->targetAmount = self::convertMoneyToFloat($event->newTargetAmount());
        $this->startDate = $event->newCoveragePeriod()->getStartDate()->format('Y-m-d');
        $this->endDate = $event->newCoveragePeriod()->getEndDate()->format('Y-m-d');
        $this->updateCalculatedFields();
    }

    public function updateFromContribution(CostContributionReceived $event): void
    {
        $this->currentAmount = self::convertMoneyToFloat($event->newTotalCoveredAmount());
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
        $currentDate = new \DateTimeImmutable();
        $startDate = new \DateTimeImmutable($this->startDate);
        $endDate = new \DateTimeImmutable($this->endDate);

        $this->isActiveNow = $currentDate >= $startDate && $currentDate <= $endDate;
    }

    private static function convertMoneyToFloat(Money $money): float
    {
        return $money->toFloat();
    }
}
