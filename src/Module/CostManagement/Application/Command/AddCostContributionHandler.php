<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Command;

use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use App\Module\CostManagement\Domain\Repository\CostItemRepositoryInterface;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\SharedContext\Domain\ValueObject\Money;
use App\Module\SharedContext\Domain\ValueObject\ProductId;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Gère la commande d'ajout d'une contribution à un poste de coût.
 */
#[AsCommandHandler]
final readonly class AddCostContributionHandler
{
    public function __construct(
        private CostItemRepositoryInterface $costItemRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(AddCostContribution $command): void
    {
        $dto = $command->dto;
        $costItemId = CostItemId::fromString($dto->costItemId);

        $costItem = $this->costItemRepository->findOrFail($costItemId);

        $amount = Money::fromFloat($dto->amount);
        $sourceProductId = $dto->sourceProductId ? ProductId::fromString($dto->sourceProductId) : null;

        $costItem->addContribution($amount, $sourceProductId);

        $this->costItemRepository->save($costItem, true);

        foreach ($costItem->pullDomainEvents() as $domainEvent) {
            $this->eventDispatcher->dispatch($domainEvent);
        }
    }
}
