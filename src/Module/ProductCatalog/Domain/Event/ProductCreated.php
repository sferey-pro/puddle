<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Domain\Event;

use App\Module\SharedContext\Domain\ValueObject\ProductId;
use App\Shared\Domain\Event\DomainEvent;

final readonly class ProductCreated extends DomainEvent
{
    public function __construct(
        private ProductId $productId,
    ) {
        parent::__construct($this->productId);
    }

    public static function eventName(): string
    {
        return 'product_catalog.product.created';
    }

    public function productId(): ProductId
    {
        return $this->productId;
    }
}
