<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Domain\Event;

use App\Module\SharedContext\Domain\ValueObject\ProductId;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\DomainEventInterface;

final class ProductCostStructureUpdated extends DomainEvent implements DomainEventInterface
{
    public function __construct(
        private(set) ProductId $id,
    ) {
        parent::__construct();
    }

    public function id(): ProductId
    {
        return $this->id;
    }
}
