<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Domain\ValueObject;

use App\Module\ProductCatalog\Domain\Enum\CostComponentType;
use App\Module\SharedContext\Domain\ValueObject\Money;
use Webmozart\Assert\Assert;

final class CostComponentLine
{
    public readonly string $name; // Ex: "Loyer", "Grain de café Arabica"
    public readonly CostComponentType $type;
    public readonly Money $cost; // Le coût de cette composante pour une unité du produit
    public readonly ?Quantity $quantity; // Null si non applicable (ex: "Part du Loyer").

    public function __construct(
        string $name,
        CostComponentType $type,
        Money $cost,
        ?Quantity $quantity = null,
    ) {
        Assert::notEmpty($name, 'Cost component name cannot be empty.');
        Assert::maxLength($name, 150, 'Cost component name cannot be longer than 150 characters.');

        if (CostComponentType::RAW_MATERIAL === $type) {
            Assert::notNull($quantity, 'Quantity must be provided for raw materials.');
        }

        $this->name = $name;
        $this->type = $type;
        $this->cost = $cost;
        $this->quantity = $quantity;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): CostComponentType
    {
        return $this->type;
    }

    public function getCost(): Money
    {
        return $this->cost;
    }

    public function getQuantity(): ?Quantity
    {
        return $this->quantity;
    }

    public function isEqualTo(self $other): bool
    {
        $quantityIsEqual = false;
        if (null === $this->quantity && null === $other->quantity) {
            $quantityIsEqual = true;
        } elseif (null !== $this->quantity && null !== $other->quantity) {
            $quantityIsEqual = $this->quantity->isEqualTo($other->quantity);
        }

        return $this->name === $other->name
            && $this->type === $other->type
            && $this->cost->isEqualTo($other->cost)
            && $quantityIsEqual;
    }
}
