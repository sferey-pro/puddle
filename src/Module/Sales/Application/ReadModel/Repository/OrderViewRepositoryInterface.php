<?php

declare(strict_types=1);

namespace App\Module\Sales\Application\ReadModel\Repository;

use App\Module\Sales\Application\ReadModel\OrderView;
use App\Module\Sales\Domain\ValueObject\OrderId;

interface OrderViewRepositoryInterface
{
    public function save(OrderView $orderView): void;

    public function ofId(OrderId $id): ?OrderView;

    /** @return OrderView[] */
    public function findAll(): array;
}
