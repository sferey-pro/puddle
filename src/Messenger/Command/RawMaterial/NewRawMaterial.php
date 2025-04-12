<?php

declare(strict_types=1);

namespace App\Messenger\Command\RawMaterial;

use App\Common\Command\CommandInterface;
use App\Entity\Category;
use App\Entity\Supplier;

final class NewRawMaterial implements CommandInterface
{
    private string $name;

    private float $unitPrice;

    private Supplier $supplier;

    private bool $priceVariability;

    private Category $category;

    private string $unit;

    private float $totalCost;

    private string $notes;

    private string $link;

    public function __construct(
        string $name,
        float $unitPrice,
        Supplier $supplier,
        bool $priceVariability,
        Category $category,
        string $unit,
        float $totalCost,
        string $notes,
        string $link,
    ) {
        $this->name = $name;
        $this->unitPrice = $unitPrice;
        $this->supplier = $supplier;
        $this->priceVariability = $priceVariability;
        $this->category = $category;
        $this->unit = $unit;
        $this->totalCost = $totalCost;
        $this->notes = $notes;
        $this->link = $link;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }

    public function getSupplier(): Supplier
    {
        return $this->supplier;
    }

    public function isPriceVariability(): bool
    {
        return $this->priceVariability;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function getTotalCost(): float
    {
        return $this->totalCost;
    }

    public function getNotes(): string
    {
        return $this->notes;
    }

    public function getLink(): string
    {
        return $this->link;
    }
}
