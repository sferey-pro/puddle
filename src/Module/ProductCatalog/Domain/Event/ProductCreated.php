<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Domain\Event;

use App\Module\ProductCatalog\Domain\ValueObject\ProductId;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\DomainEventInterface;

final class ProductCreated extends DomainEvent implements DomainEventInterface
{
    public function __construct(
        private(set) ProductId $identifier,
    ) {
        parent::__construct();
    }

    public function identifier(): ProductId
    {
        return $this->identifier;
    }
}
