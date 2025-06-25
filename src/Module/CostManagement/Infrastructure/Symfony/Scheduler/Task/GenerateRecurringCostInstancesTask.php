<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Infrastructure\Symfony\Scheduler\Task;

use App\Core\Application\Clock\ClockInterface;
use App\Core\Application\Command\CommandBusInterface;
use App\Module\CostManagement\Application\Command\CreateCostItem;
use App\Module\CostManagement\Application\DTO\CreateCostItemDTO;
use App\Module\CostManagement\Domain\Repository\CostItemRepositoryInterface;
use App\Module\CostManagement\Domain\Repository\RecurringCostRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Scheduler\Attribute\AsPeriodicTask;

/**
 * Tâche planifiée pour générer les instances de CostItem à partir des modèles récurrents.
 * et est configurée pour s'exécuter quotidiennement.
 */
#[AsPeriodicTask(frequency: '1 day', jitter: 6, from: '08:00:00', until: '22:00:00')]
final class GenerateRecurringCostInstancesTask
{
    public function __construct(
        private readonly RecurringCostRepositoryInterface $recurringCostRepository,
        private readonly CostItemRepositoryInterface $costItemRepository,
        private readonly CommandBusInterface $commandBus,
        private readonly ClockInterface $clock,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(): void
    {
        $now = $this->clock->now();
        $this->logger->info('Running GenerateRecurringCostInstancesTask.', ['date' => $now->format('Y-m-d')]);

        $dueRecurringCosts = $this->recurringCostRepository->findDueForGeneration($now);

        if (0 === \count($dueRecurringCosts)) {
            $this->logger->info('No recurring cost items are due for generation today.');

            return;
        }

        $this->logger->info(\sprintf('Found %d recurring cost(s) to generate.', \count($dueRecurringCosts)));

        foreach ($dueRecurringCosts as $recurringCost) {
            // 1. Charger le CostItem qui sert de modèle
            $templateCostItem = $this->costItemRepository->ofId($recurringCost->templateCostItemId());

            if (!$templateCostItem || !$templateCostItem->isTemplate()) {
                $this->logger->error('Template CostItem not found or is not a valid template.', [
                    'recurringCostId' => (string) $recurringCost->id(),
                    'templateCostItemId' => (string) $recurringCost->templateCostItemId(),
                ]);
                continue;
            }

            // 2. Préparer les données pour la nouvelle instance
            $instanceName = \sprintf('%s - %s', $templateCostItem->name(), $now->format('F Y'));

            // 3. Calculer dynamiquement la période de couverture
            $recurrenceRule = $recurringCost->recurrenceRule();
            $durationModifier = $recurrenceRule->getDurationModifier();
            $startDate = $now;
            $endDate = $startDate->modify($durationModifier);

            // 4. Remplir le DTO
            $dto = new CreateCostItemDTO();
            $dto->name = $instanceName;
            $dto->type = (string) $templateCostItem->type();
            $dto->currency = $templateCostItem->targetAmount()->getCurrency();
            $dto->targetAmount = $templateCostItem->targetAmount() ? $templateCostItem->targetAmount()->getAmount() : 0;
            $dto->startDate = $startDate;
            $dto->endDate = $endDate;

            $this->logger->info('Dispatching CreateCostItem command for new instance.', [
                'recurringCostId' => (string) $recurringCost->id(),
                'templateId' => (string) $templateCostItem->id(),
                'coveragePeriod' => \sprintf('From %s to %s', $startDate->format('Y-m-d'), $endDate->format('Y-m-d')),
            ]);

            // 5. Envoyer la commande de création
            $this->commandBus->dispatch(new CreateCostItem($dto));

            // 6. Marquer le RecurringCost comme traité pour cette période
            $recurringCost->markAsGenerated($this->clock);
            $this->recurringCostRepository->save($recurringCost, true);
        }

        $this->logger->info('Successfully processed all due recurring costs.');
    }
}
