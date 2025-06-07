<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Command;

use App\Module\CostManagement\Domain\Exception\CostItemException;
use App\Module\CostManagement\Domain\Repository\CostItemRepositoryInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Gère l'exécution de la commande de réactivation d'un poste de coût.
 */
#[AsCommandHandler]
final readonly class ReactivateCostItemHandler
{
    public function __construct(
        private readonly CostItemRepositoryInterface $repository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * Exécute la commande.
     */
    public function __invoke(ReactivateCostItem $command): void
    {
        $costItem = $this->repository->findOrFail($command->costItemId);

        if (!$costItem) {
            throw CostItemException::notFoundWithId($command->costItemId);
        }

        $costItem->reactivate();

        $this->repository->save($costItem, true);

        foreach ($costItem->pullDomainEvents() as $domainEvent) {
            $this->eventDispatcher->dispatch($domainEvent);
        }
    }
}
