<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Command;

use App\Module\CostManagement\Domain\CostContribution;
use App\Module\CostManagement\Domain\Repository\CostItemRepositoryInterface;
use App\Module\CostManagement\Domain\ValueObject\CostContributionId;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\SharedContext\Domain\ValueObject\Money;
use App\Module\SharedContext\Domain\ValueObject\ProductId;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Gère la commande de suppression d'une contribution.
 */
#[AsCommandHandler]
final readonly class UpdateCostContributionHandler
{
    public function __construct(
        private CostItemRepositoryInterface $repository,
        private EntityManagerInterface $entityManager,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(UpdateCostContribution $command): void
    {
        $dto = $command->dto;
        $costItemId = CostItemId::fromString($dto->costItemId);
        $costItem = $this->repository->findOrFail($costItemId);

        $costItem->updateContribution(
            CostContributionId::fromString($dto->contributionId),
            Money::fromFloat($dto->amount),
            $dto->sourceProductId ? ProductId::fromString($dto->sourceProductId) : null
        );

        $this->repository->save($costItem, true);

        foreach ($costItem->pullDomainEvents() as $domainEvent) {
            $this->eventDispatcher->dispatch($domainEvent);
        }
    }
}
