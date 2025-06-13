<?php

declare(strict_types=1);

namespace App\Module\Sales\Application\ReadModel;

class OrderLineView
{
    public function __construct(
        public readonly string $productId,
        public readonly int $quantity,
        public readonly int $unitPriceAmount,
        public readonly string $currency,
        public readonly int $totalAmount
    ) {
    }
}
