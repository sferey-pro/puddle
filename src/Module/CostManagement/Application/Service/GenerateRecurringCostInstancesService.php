<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Service;

use App\Core\Application\Clock\ClockInterface;
use App\Core\Application\Command\CommandBusInterface;
use App\Module\CostManagement\Application\Command\CreateCostItem;
use App\Module\CostManagement\Application\DTO\CreateCostItemDTO;
use App\Module\CostManagement\Domain\Repository\RecurringCostRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Service responsable de la génération des instances de CostItem
 * à partir des modèles de coûts récurrents.
 * Cette classe est conçue pour être appelée par une tâche planifiée.
 */
final class GenerateRecurringCostInstancesService
{
    public function __construct(
        private readonly RecurringCostRepositoryInterface $repository,
        private readonly CommandBusInterface $commandBus,
        private readonly ClockInterface $clock,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Point d'entrée du service.
     * Recherche les coûts récurrents dus et déclenche leur création.
     */
    public function __invoke(): void
    {
        $now = $this->clock->now();
        $this->logger->info('Running GenerateRecurringCostInstancesService.', ['date' => $now->format('Y-m-d')]);

        $dueItems = $this->repository->findDueForGeneration($now);

        if (0 === \count($dueItems)) {
            $this->logger->info('No recurring cost items are due for generation today.');

            return;
        }

        $this->logger->info(\sprintf('Found %d recurring cost(s) to generate.', \count($dueItems)));

        foreach ($dueItems as $dueItem) {
            // Prépare le nom du CostItem, par exemple "Loyer - Juin 2025"
            $instanceName = \sprintf(
                '%s - %s',
                $dueItem->name(),
                $now->format('F Y')
            );

            $this->logger->info('Dispatching CreateCostItem command.', [
                'recurringCostId' => (string) $dueItem->id(),
                'instanceName' => $instanceName,
            ]);

            $dto = new CreateCostItemDTO();
            $dto->name = $instanceName;
            $dto->type = $dueItem->type();
            $dto->amount = $dueItem->amount()->getAmount();
            $dto->targetAmount = $dueItem->amount()->getAmount();

            $this->commandBus->dispatch(new CreateCostItem($dto));

            // Met à jour le modèle pour ne pas le régénérer pour cette période
            $dueItem->markAsGenerated($this->clock);
            $this->repository->save($dueItem, true);
        }

        $this->logger->info('Successfully generated all due recurring cost items.');
    }
}
