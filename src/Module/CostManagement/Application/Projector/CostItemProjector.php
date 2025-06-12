<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Projector;

use App\Module\CostManagement\Application\ReadModel\CostItemInstanceView;
use App\Module\CostManagement\Application\ReadModel\CostItemTemplateView;
use App\Module\CostManagement\Application\ReadModel\Repository\CostItemInstanceViewRepositoryInterface;
use App\Module\CostManagement\Application\ReadModel\Repository\CostItemTemplateViewRepositoryInterface;
use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\Enum\CostItemStatus;
use App\Module\CostManagement\Domain\Event\CostContributionReceived;
use App\Module\CostManagement\Domain\Event\CostContributionRemoved;
use App\Module\CostManagement\Domain\Event\CostContributionUpdated;
use App\Module\CostManagement\Domain\Event\CostItemAdded;
use App\Module\CostManagement\Domain\Event\CostItemArchived;
use App\Module\CostManagement\Domain\Event\CostItemCovered;
use App\Module\CostManagement\Domain\Event\CostItemDetailsUpdated;
use App\Module\CostManagement\Domain\Event\CostItemReactivated;
use App\Module\CostManagement\Domain\Event\CostItemReopened;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Le CostItemProjector écoute les événements de domaine du CostItem
 * et met à jour le ReadModel (CostItemInstanceView & CostItemTemplateView). Son rôle est d'orchestrer
 * la mise à jour en déléguant toute la logique au ReadModel lui-même.
 */
final class CostItemProjector implements EventSubscriberInterface
{
    public function __construct(
        private readonly CostItemInstanceViewRepositoryInterface $instanceRepository,
        private readonly CostItemTemplateViewRepositoryInterface $templateRepository,
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
            CostItemReopened::class => 'onCostItemReopened',
            CostContributionReceived::class => 'onCostContributionReceived',
            CostContributionUpdated::class => 'onCostContributionUpdated',
            CostContributionRemoved::class => 'onCostContributionRemoved',
        ];
    }

    private function save(CostItemInstanceView|CostItemTemplateView $view, bool $flush = false): void
    {
        $view instanceof CostItemTemplateView ?
            $this->templateRepository->save($view, $flush) :
            $this->instanceRepository->save($view, $flush);
    }

    public function onCostItemAdded(CostItemAdded $event): void
    {
        $view = $event->isTemplate() ?
            CostItemTemplateView::fromCostItemAdded($event) :
            CostItemInstanceView::fromCostItemAdded($event);

        $this->save($view, true);
    }

    public function onCostItemDetailsUpdated(CostItemDetailsUpdated $event): void
    {
        $view = $this->findView($event->costItemId());
        if (!$view) {
            return;
        }

        $view->updateFromDetails($event);
        $this->save($view, true);
    }

    public function onCostContributionReceived(CostContributionReceived $event): void
    {
        $view = $this->findView($event->costItemId());
        if (!$view) {
            return;
        }

        $view->applyCostContributionReceived($event);
        $this->save($view, true);
    }

    /**
     * Gère la mise à jour d'une contribution dans la vue.
     */
    public function onCostContributionUpdated(CostContributionUpdated $event): void
    {
        $view = $this->findView($event->costItemId());
        if (!$view) {
            return;
        }

        $view->applyCostContributionUpdated($event);
        $this->save($view, true);
    }

    public function onCostContributionRemoved(CostContributionRemoved $event): void
    {
        $view = $this->findView($event->costItemId());
        if (!$view) {
            return;
        }

        $view->applyCostContributionRemoved($event);
        $this->save($view, true);
    }

    public function onCostItemCovered(CostItemCovered $event): void
    {
        $view = $this->findView($event->costItemId());
        if (!$view) {
            return;
        }

        $view->updateStatus('fully_covered');
        $this->save($view, true);
    }

    public function onCostItemArchived(CostItemArchived $event): void
    {
        $view = $this->findView($event->costItemId());
        if (!$view) {
            return;
        }

        $view->updateStatus('archived');
        $this->save($view, true);
    }

    public function onCostItemReactivated(CostItemReactivated $event): void
    {
        $view = $this->findView($event->costItemId());
        if (!$view) {
            return;
        }

        $view->updateStatus($event->newStatus()->value);
        $this->save($view, true);
    }

    public function onCostItemReopened(CostItemReopened $event): void
    {
        $view = $this->findView($event->costItemId());
        if (!$view) {
            return;
        }

        $view->updateStatus(CostItemStatus::ACTIVE->value);
        $this->save($view, true);
    }

    private function findView(CostItemId $costItemId): ?CostItemInstanceView
    {
        // Utilisation de `findById` au lieu de `findOrFail` pour éviter une exception
        // si un événement arrive avant que la vue ne soit créée (cas rare mais possible).
        return $this->instanceRepository->findById($costItemId);
    }
}
