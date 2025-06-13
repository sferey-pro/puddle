<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Domain;

use App\Module\ProductCatalog\Domain\Event\ProductCreated;
use App\Module\ProductCatalog\Domain\ValueObject\BaseCostStructure;
use App\Module\SharedContext\Domain\ValueObject\ProductId;
use App\Module\ProductCatalog\Domain\ValueObject\ProductName;
use App\Module\SharedContext\Domain\ValueObject\Money;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Model\DomainEventTrait;

class Product extends AggregateRoot
{
    use DomainEventTrait;

    private ProductName $name;
    private BaseCostStructure $baseCostStructure;
    private bool $isActive = true;

    private function __construct(
        private ProductId $identifier,
        ProductName $name,
        BaseCostStructure $baseCostStructure,
    ) {
        $this->name = $name;
        $this->baseCostStructure = $baseCostStructure;
    }

    public static function create(
        ProductId $identifier,
        ProductName $name,
        BaseCostStructure $baseCostStructure,
    ): self {
        $product = new self($identifier, $name, $baseCostStructure);

        $product->recordDomainEvent(
            new ProductCreated(
                identifier: $product->identifier(),
            )
        );

        return $product;
    }

    public function id(): ProductId
    {
        return $this->identifier;
    }

    public function identifier(): ProductId
    {
        return $this->identifier;
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
