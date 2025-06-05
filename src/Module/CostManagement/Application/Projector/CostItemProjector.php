<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Projector;

use App\Module\CostManagement\Application\ReadModel\CostItemView;
use App\Module\CostManagement\Application\ReadModel\Repository\CostItemViewRepositoryInterface;
use App\Module\CostManagement\Domain\Event\CostContributionReceived;
use App\Module\CostManagement\Domain\Event\CostItemAdded;
use App\Module\CostManagement\Domain\Event\CostItemArchived;
use App\Module\CostManagement\Domain\Event\CostItemCovered;
use App\Module\CostManagement\Domain\Event\CostItemDetailsUpdated;
use App\Module\CostManagement\Domain\Event\CostItemReactivated;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\SharedContext\Domain\ValueObject\Money;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @docblock
 * Le CostItemProjector écoute les événements de domaine du CostItem
 * et met à jour le ReadModel (CostItemView) dans la base de données de lecture (MongoDB).
 */
final class CostItemProjector implements EventSubscriberInterface
{
    public function __construct(
        private readonly CostItemViewRepositoryInterface $repository,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CostItemAdded::class => 'onCostItemAdded',
            CostItemDetailsUpdated::class => 'onCostItemDetailsUpdated',
            CostContributionReceived::class => 'onCostContributionReceived',
            CostItemCovered::class => 'onCostItemCovered',
            CostItemArchived::class => 'onCostItemArchived',
            CostItemReactivated::class => 'onCostItemReactivated',
        ];
    }

    public function onCostItemAdded(CostItemAdded $event): void
    {
        $targetAmountFloat = $this->convertMoneyToFloat($event->targetAmount());
        $currentDate = new \DateTimeImmutable();
        $startDate = $event->coveragePeriod()->getStartDate();
        $endDate = $event->coveragePeriod()->getEndDate();

        $view = new CostItemView(
            id: (string) $event->costItemId(),
            name: (string) $event->name(),
            targetAmount: $targetAmountFloat,
            currentAmount: 0.0,
            currency: $event->targetAmount()->getCurrency(),
            startDate: $startDate->format('Y-m-d'),
            endDate: $endDate->format('Y-m-d'),
            status: $event->status()->value,
            progressPercentage: 0.0,
            isCovered: false,
            isActiveNow: $currentDate >= $startDate && $currentDate <= $endDate
        );

        $this->repository->save($view, true);
    }

    public function onCostItemDetailsUpdated(CostItemDetailsUpdated $event): void
    {
        $view = $this->findView($event->costItemId());
        if (!$view) {
            return;
        }

        $view->name = (string) $event->newName();
        $view->targetAmount = $this->convertMoneyToFloat($event->newTargetAmount());
        $view->startDate = $event->newCoveragePeriod()->getStartDate()->format('Y-m-d');
        $view->endDate = $event->newCoveragePeriod()->getEndDate()->format('Y-m-d');

        // Recalculer la progression et le statut "actif"
        $this->recalculateProgress($view);
        $this->recalculateIsActiveNow($view);

        $this->repository->save($view, true);
    }

    public function onCostContributionReceived(CostContributionReceived $event): void
    {
        $view = $this->findView($event->costItemId());
        if (!$view) {
            return;
        }

        $view->currentAmount = $this->convertMoneyToFloat($event->newTotalCoveredAmount());
        $this->recalculateProgress($view);

        $this->repository->save($view, true);
    }

    public function onCostItemCovered(CostItemCovered $event): void
    {
        $view = $this->findView($event->costItemId());
        if (!$view) {
            return;
        }

        $view->isCovered = true;
        $view->status = 'fully_covered'; // Mettre à jour le statut
        $this->recalculateProgress($view); // La progression devrait être à 100% ou plus

        $this->repository->save($view, true);
    }

    public function onCostItemArchived(CostItemArchived $event): void
    {
        $view = $this->findView($event->costItemId());
        if (!$view) {
            return;
        }
        $view->status = 'archived';
        $this->recalculateIsActiveNow($view);
        $this->repository->save($view, true);
    }

    public function onCostItemReactivated(CostItemReactivated $event): void
    {
        $view = $this->findView($event->costItemId());
        if (!$view) {
            return;
        }
        $view->status = $event->newStatus()->value;
        $this->recalculateIsActiveNow($view);
        $this->repository->save($view, true);
    }

    private function findView(CostItemId $costItemId): ?CostItemView
    {
        return $this->repository->findById((string) $costItemId);
    }

    private function convertMoneyToFloat(Money $money): float
    {
        // Supposant que votre objet Money a une méthode toFloat() ou similaire
        // qui divise le montant en centimes par 100.
        return $money->toFloat();
    }

    private function recalculateProgress(CostItemView $view): void
    {
        if ($view->targetAmount > 0) {
            $view->progressPercentage = min(100.0, ($view->currentAmount / $view->targetAmount) * 100);
        } else {
            $view->progressPercentage = 100.0; // Si la cible est 0, on considère couvert
        }
    }

    private function recalculateIsActiveNow(CostItemView $view): void
    {
        $currentDate = new \DateTimeImmutable();
        $startDate = new \DateTimeImmutable($view->startDate);
        $endDate = new \DateTimeImmutable($view->endDate);

        $view->isActiveNow = $currentDate >= $startDate && $currentDate <= $endDate;
    }
}
