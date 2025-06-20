<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Domain;

use App\Module\ProductCatalog\Domain\Event\ProductCreated;
use App\Module\ProductCatalog\Domain\ValueObject\BaseCostStructure;
use App\Module\ProductCatalog\Domain\ValueObject\ProductName;
use App\Module\SharedContext\Domain\ValueObject\Money;
use App\Module\SharedContext\Domain\ValueObject\ProductId;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Event\DomainEventTrait;

class Product extends AggregateRoot
{
    use DomainEventTrait;

    private ProductName $name;
    private BaseCostStructure $baseCostStructure;
    private bool $isActive = true;

    private function __construct(
        private ProductId $id,
        ProductName $name,
        BaseCostStructure $baseCostStructure,
    ) {
        $this->name = $name;
        $this->baseCostStructure = $baseCostStructure;
    }

    public static function create(
        ProductId $id,
        ProductName $name,
        BaseCostStructure $baseCostStructure,
    ): self {
        $product = new self($id, $name, $baseCostStructure);

        $product->recordDomainEvent(
            new ProductCreated(
                id: $product->id(),
            )
        );

        return $product;
    }

    public function id(): ProductId
    {
        return $this->id;
    }

    public function name(): ProductName
    {
        return $this->name;
    }

    public function baseCostStructure(): BaseCostStructure
    {
        return $this->baseCostStructure;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function changeName(ProductName $newName): void
    {
        if (!$this->name->isEqualTo($newName)) {
            $this->name = $newName;
        }
    }

    public function updateCostStructure(BaseCostStructure $newStructure): void
    {
        $this->baseCostStructure = $newStructure;
    }

    public function activate(): void
    {
        $this->isActive = true;
    }

    public function deactivate(): void
    {
        $this->isActive = false;
    }

    public function totalBaseCost(): Money
    {
        return $this->baseCostStructure->totalBaseCost();
    }
}
