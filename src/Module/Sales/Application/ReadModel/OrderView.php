<?php

declare(strict_types=1);

namespace App\Module\Sales\Application\ReadModel;

class OrderView
{
    /** @var array<OrderLineView> */
    public array $orderLines = [];

    public function __construct(
        public readonly string $id,
        public readonly string $userId,
        public readonly string $status,
        public readonly float $totalAmount,
        public readonly string $currency,
        public readonly \DateTimeImmutable $createdAt,
    ) {
    }
}
