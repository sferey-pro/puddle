<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Repository;

use App\Module\CostManagement\Domain\RecurringCost;
use App\Module\CostManagement\Domain\ValueObject\RecurringCostId;
use DateTimeInterface;

interface RecurringCostRepositoryInterface
{
    public function save(RecurringCost $recurringCost): void;

    public function ofId(RecurringCostId $id): ?RecurringCost;

    /**
     * @return RecurringCost[]
     */
    public function findDueForGeneration(DateTimeInterface $dateTime): array;
}
