<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Repository;

use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Shared\Domain\Repository\RepositoryInterface;

/**
 * @extends RepositoryInterface<CostItem>
 */
interface CostItemRepositoryInterface extends RepositoryInterface
{
    public function save(CostItem $model, bool $flush = false): void;

    public function add(CostItem $model): void;

    public function remove(CostItem $model): void;

    public function ofIdentifier(CostItemId $identifier): ?CostItem;

    /**
     * @return CostItem[]
     */
    public function findActiveAndUncovered(): array;
}
