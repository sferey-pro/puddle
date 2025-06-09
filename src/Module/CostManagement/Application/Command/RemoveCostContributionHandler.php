<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Command;

use App\Module\CostManagement\Domain\Repository\CostItemRepositoryInterface;
use App\Module\CostManagement\Domain\ValueObject\CostContributionId;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Gère la commande de suppression d'une contribution.
 */
#[AsCommandHandler]
final readonly class RemoveCostContributionHandler
{
    public function __construct(
        private CostItemRepositoryInterface $costItemRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(RemoveCostContribution $command): void
    {
        $costItemId = CostItemId::fromString($command->costItemId);
        $costItem = $this->costItemRepository->findOrFail($costItemId);

        $contributionId = CostContributionId::fromString($command->contributionId);
        $costItem->removeContribution($contributionId);

        $this->costItemRepository->save($costItem, true);

        foreach ($costItem->pullDomainEvents() as $domainEvent) {
            $this->eventDispatcher->dispatch($domainEvent);
        }
    }
}
