<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Domain\Event;

use App\Module\SharedContext\Domain\ValueObject\Money;
use App\Module\SharedContext\Domain\ValueObject\ProductId; // Assuming Money ValueObject
use App\Shared\Domain\Event\DomainEvent;

final readonly class ProductPriceChanged extends DomainEvent
{
    public function __construct(
        private ProductId $productId,
        private Money $oldPrice,
        private Money $newPrice,
    ) {
        parent::__construct($this->productId);
    }

    public static function eventName(): string
    {
        return 'product_catalog.product.price_changed';
    }

    public function productId(): ProductId
    {
        return $this->productId;
    }

    public function oldPrice(): Money
    {
        return $this->oldPrice;
    }

    public function newPrice(): Money
    {
        return $this->newPrice;
    }
}
