<?php

declare(strict_types=1);

namespace App\Module\Sales\Domain;

use App\Module\Sales\Domain\ValueObject\OrderLineId;
use App\Module\SharedContext\Domain\ValueObject\Money;
use App\Module\SharedContext\Domain\ValueObject\ProductId;
use Webmozart\Assert\Assert;

final class OrderLine
{
    private Order $order;

    public readonly ProductId $productId;
    public readonly int $quantity;
    public readonly Money $unitPrice;

    public function __construct(
        private OrderLineId $id,
        ProductId  $productId,
        int $quantity,
        Money $unitPrice,
    ) {
        Assert::greaterThan($quantity, 0, 'La quantité doit être supérieure à 0.');

        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
    }

    public static function create($productId, $quantity, $unitPrice): self
    {
        $id = OrderLineId::generate();
        $orderLine = new self($id, $productId, $quantity, $unitPrice);

        return $orderLine;
    }

    public function calculateTotal(): Money
    {
        return $this->unitPrice->multiplyBy($this->quantity);
    }

    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }
}
