<?php

declare(strict_types=1);

namespace App\Messenger\Command\Product;

use App\Common\Command\CommandInterface;
use App\Entity\Category;

final class NewProduct implements CommandInterface
{
    private string $name;

    private float $price;

    private Category $category;

    public function __construct(string $name, float $price, Category $category)
    {
        $this->name = $name;
        $this->price = $price;
        $this->category = $category;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }
}
