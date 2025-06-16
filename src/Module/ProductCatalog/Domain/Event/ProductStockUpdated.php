<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Domain\Event;

use App\Module\SharedContext\Domain\ValueObject\ProductId;
use App\Shared\Domain\Event\DomainEvent;

final readonly class ProductStockUpdated extends DomainEvent
{
    public function __construct(
        private ProductId $productId,
        private int $oldStockLevel,
        private int $newStockLevel,
    ) {
        parent::__construct($this->productId);
    }

    public static function eventName(): string
    {
        return 'product_catalog.product.stock_updated';
    }

    public function productId(): ProductId
    {
        return $this->productId;
    }

    public function oldStockLevel(): int
    {
        return $this->oldStockLevel;
    }

    public function newStockLevel(): int
    {
        return $this->newStockLevel;
    }
}
