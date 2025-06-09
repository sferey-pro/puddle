<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Command;

use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\Enum\CostItemType;
use App\Module\CostManagement\Domain\Repository\CostItemRepositoryInterface;
use App\Module\CostManagement\Domain\ValueObject\CostItemName;
use App\Module\CostManagement\Domain\ValueObject\CoveragePeriod;
use App\Module\SharedContext\Domain\ValueObject\Money;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsCommandHandler]
final class CreateCostItemHandler
{
    public function __construct(
        private readonly CostItemRepositoryInterface $repository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(CreateCostItem $command): void
    {
        $dto = $command->dto;

        // Création de l'agrégat à partir des données du DTO
        $costItem = CostItem::create(
            name: new CostItemName($dto->name),
            type: CostItemType::from($dto->type),
            targetAmount: new Money($dto->targetAmount, $dto->currency),
            coveragePeriod: CoveragePeriod::create(
                $dto->startDate,
                $dto->endDate
            ),
            description: $dto->description
        );

        // Sauvegarde
        $this->repository->save($costItem, true);

        // Dispatch les événements de domaine
        foreach ($costItem->pullDomainEvents() as $domainEvent) {
            $this->eventDispatcher->dispatch($domainEvent);
        }
    }
}
