<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Command;

use App\Module\CostManagement\Domain\Exception\CostItemException;
use App\Module\CostManagement\Domain\Repository\CostItemRepositoryInterface;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\CostManagement\Domain\ValueObject\CostItemName;
use App\Module\CostManagement\Domain\ValueObject\CoveragePeriod;
use App\Module\SharedContext\Domain\ValueObject\Money;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsCommandHandler]
final class UpdateCostItemHandler
{
    public function __construct(
        private readonly CostItemRepositoryInterface $repository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(UpdateCostItem $command): void
    {
        $dto = $command->dto;
        $costItemId = CostItemId::fromString($dto->id);

        $costItem = $this->repository->ofId($costItemId);

        if (!$costItem) {
            throw CostItemException::notFoundWithId($costItemId);
        }

        $costItem->updateDetails(
            name: new CostItemName($dto->name),
            targetAmount: new Money($dto->targetAmount, $dto->currency),
            coveragePeriod: CoveragePeriod::create(
                $dto->startDate,
                $dto->endDate
            ),
            description: $dto->description
        );

        $this->repository->save($costItem, true);

        foreach ($costItem->pullDomainEvents() as $domainEvent) {
            $this->eventDispatcher->dispatch($domainEvent);
        }
    }
}
