<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Projector;

use App\Module\CostManagement\Application\ReadModel\ContributionView;
use App\Module\CostManagement\Application\ReadModel\CostItemView;
use App\Module\CostManagement\Application\ReadModel\Repository\CostItemViewRepositoryInterface;
use App\Module\CostManagement\Domain\Event\CostContributionReceived;
use App\Module\CostManagement\Domain\Event\CostContributionRemoved;
use App\Module\CostManagement\Domain\Event\CostItemAdded;
use App\Module\CostManagement\Domain\Event\CostItemArchived;
use App\Module\CostManagement\Domain\Event\CostItemCovered;
use App\Module\CostManagement\Domain\Event\CostItemDetailsUpdated;
use App\Module\CostManagement\Domain\Event\CostItemReactivated;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @docblock
 * Le CostItemProjector écoute les événements de domaine du CostItem
 * et met à jour le ReadModel (CostItemView) dans la base de données de lecture.
 * Son rôle est d'orchestrer la mise à jour en déléguant la logique de
 * construction et de modification au ReadModel CostItemView lui-même.
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
            CostItemCovered::class => 'onCostItemCovered',
            CostItemArchived::class => 'onCostItemArchived',
            CostItemReactivated::class => 'onCostItemReactivated',
        ];
    }

    public function onCostItemAdded(CostItemAdded $event): void
    {
        $view = CostItemView::fromCostItemAdded($event);
        $this->repository->save($view, true);
    }

    public function onCostItemDetailsUpdated(CostItemDetailsUpdated $event): void
    {
        $view = $this->findView($event->costItemId());
        if (!$view) {
            return;
        }

        $view->updateFromDetails($event);
        $this->repository->save($view, true);
    }

    public function onCostContributionReceived(CostContributionReceived $event): void
    {
        $costItemView = $this->costItemViewRepository->findOrFail($event->costItemId->value);

        $contributionView = new ContributionView(
            id: $event->contributionId->value,
            amount: $event->amount->toFloat(),
            currency: $event->amount->getCurrency()->getCode(),
            contributedAt: new \DateTimeImmutable($event->occurredOn()),
            sourceProductId: $event->sourceProductId?->value
        );

        // Ajoute la nouvelle contribution à la liste
        $costItemView->contributions[] = $contributionView;

        // Met à jour le total couvert
        $costItemView->currentAmountCovered = $event->newTotalCovered->toFloat();

        $this->costItemViewRepository->save($costItemView);
    }

    public function onCostContributionRemoved(CostContributionRemoved $event): void
    {
        $costItemView = $this->costItemViewRepository->findOrFail($event->costItemId->value);

        // Filtre la liste pour retirer la contribution supprimée
        $costItemView->contributions = array_values(array_filter(
            $costItemView->contributions,
            fn (ContributionView $c) => $c->id !== $event->contributionId->value
        ));

        // Met à jour le total couvert
        $costItemView->currentAmountCovered = $event->newTotalCovered->toFloat();

        $this->costItemViewRepository->save($costItemView);
    }

    public function onCostItemCovered(CostItemCovered $event): void
    {
        $view = $this->findView($event->costItemId());
        if (!$view) {
            return;
        }

        $view->updateStatus('fully_covered');
        $this->repository->save($view, true);
    }

    public function onCostItemArchived(CostItemArchived $event): void
    {
        $view = $this->findView($event->costItemId());
        if (!$view) {
            return;
        }

        $view->updateStatus('archived');
        $this->repository->save($view, true);
    }

    public function onCostItemReactivated(CostItemReactivated $event): void
    {
        $view = $this->findView($event->costItemId());
        if (!$view) {
            return;
        }

        $view->updateStatus($event->newStatus()->value);
        $this->repository->save($view, true);
    }

    private function findView(CostItemId $costItemId): ?CostItemView
    {
        return $this->repository->findById($costItemId);
    }
}
