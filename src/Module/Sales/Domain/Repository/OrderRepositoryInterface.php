<?php

declare(strict_types=1);

namespace App\Module\Sales\Domain\Repository;

use App\Core\Domain\Repository\RepositoryInterface;
use App\Module\Sales\Domain\Order;
use App\Module\Sales\Domain\ValueObject\OrderId;

interface OrderRepositoryInterface extends RepositoryInterface
{
    public function save(Order $order): void;

    public function ofId(OrderId $id): ?Order;
}
