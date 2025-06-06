<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Command;

use App\Module\CostManagement\Domain\Repository\CostItemRepositoryInterface;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\ProductCatalog\Domain\ValueObject\ProductId;
use App\Module\SharedContext\Domain\ValueObject\Money;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;

/**
 * Gère la commande d'ajout d'une contribution à un poste de coût.
 */
#[AsCommandHandler]
final readonly class AddCostContributionHandler
{
    public function __construct(
        private CostItemRepositoryInterface $costItemRepository,
    ) {
    }

    public function __invoke(AddCostContribution $command): void
    {
        $dto = $command->dto;
        $costItemId = CostItemId::fromString($dto->costItemId);

        $costItem = $this->costItemRepository->findOrFail($costItemId);

        $amount = Money::fromFloat($dto->amount);
        $sourceProductId = $dto->sourceProductId ? new ProductId($dto->sourceProductId) : null;

        $costItem->addContribution($amount, $sourceProductId);

        $this->costItemRepository->save($costItem);
    }
}
