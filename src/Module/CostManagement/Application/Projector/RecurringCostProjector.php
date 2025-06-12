<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Projector;

use App\Module\CostManagement\Application\ReadModel\RecurringCostView;
use App\Module\CostManagement\Application\ReadModel\Repository\RecurringCostViewRepositoryInterface;
use App\Module\CostManagement\Domain\Event\RecurringCostCreated;
use App\Module\CostManagement\Domain\Repository\CostItemRepositoryInterface;
use App\Module\CostManagement\Domain\Repository\RecurringCostRepositoryInterface;
use App\Shared\Domain\Service\ClockInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Projector qui met à jour le Read Model RecurringCostView.
 * Il écoute les événements de domaine liés à RecurringCost.
 */
final class RecurringCostProjector implements EventSubscriberInterface
{
    public function __construct(
        private readonly RecurringCostViewRepositoryInterface $viewRepository,
        private readonly RecurringCostRepositoryInterface $recurringCostRepository,
        private readonly CostItemRepositoryInterface $costItemRepository,
        private readonly ClockInterface $clock
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RecurringCostCreated::class => 'onRecurringCostCreated',
        ];
    }

    /**
     * Crée une nouvelle vue lorsqu'une planification est créée.
     */
    public function onRecurringCostCreated(RecurringCostCreated $event): void
    {

        $recurringCost = $this->recurringCostRepository->ofId($event->recurringCostId());
        if (!$recurringCost) {
            return;
        }

        $template = $this->costItemRepository->ofId($recurringCost->templateCostItemId());
        if (!$template) {
            return;
        }

        $rule = $recurringCost->recurrenceRule();
        $nextGenerationDate = $rule->getNextRunDate($this->clock->now());

        $ruleAsString = $rule->frequency->toHumanReadable($rule->day);

        $view = new RecurringCostView(
            (string) $recurringCost->id(),
            (string) $template->id(),
            (string) $template->name(),
            $ruleAsString, // A améliorer pour un affichage plus humain
            $recurringCost->status(),
            $nextGenerationDate,
            $recurringCost->lastGeneratedAt()
        );

        $this->viewRepository->save($view, true);
    }
}
