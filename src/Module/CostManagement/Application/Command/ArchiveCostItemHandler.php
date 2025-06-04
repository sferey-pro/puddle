<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Command;

use App\Module\CostManagement\Domain\Exception\CostItemException;
use App\Module\CostManagement\Domain\Repository\CostItemRepositoryInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsCommandHandler]
final class ArchiveCostItemHandler
{
    public function __construct(
        private readonly CostItemRepositoryInterface $repository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(ArchiveCostItem $command): void
    {
        $costItem = $this->repository->ofId($command->costItemId);

        if (!$costItem) {
            throw CostItemException::notFoundWithId($command->costItemId);
        }

        $costItem->archive();

        $this->repository->save($costItem, true);

        foreach ($costItem->pullDomainEvents() as $domainEvent) {
            $this->eventDispatcher->dispatch($domainEvent);
        }
    }
}
