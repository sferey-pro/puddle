<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Domain\ValueObject;

use App\Module\SharedContext\Domain\ValueObject\Money;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

final class BaseCostStructure
{
    /** @var array<int, CostComponentLine> */
    public readonly array $costComponentLines;
    public readonly Money $totalBaseCost;

    /**
     * @param CostComponentLine[] $costComponentLines
     */
    public function __construct(array $costComponentLines)
    {
        Assert::allIsInstanceOf($costComponentLines, CostComponentLine::class, 'All items in cost structure must be CostComponentLine instances.');
        $this->costComponentLines = array_values($costComponentLines); // Réindexer pour s'assurer que c'est une liste

        $this->totalBaseCost = $this->calculateTotalBaseCost($costComponentLines);
    }

    /**
     * @return CostComponentLine[]
     */
    public function costComponentLines(): array
    {
        return $this->costComponentLines;
    }

    public function totalBaseCost(): Money
    {
        return $this->totalBaseCost;
    }

    /**
     * @param CostComponentLine[] $lines
     *
     * @throws InvalidArgumentException
     */
    private function calculateTotalBaseCost(array $lines): Money
    {
        if (empty($lines)) {
            return Money::zero();
        }

        $total = new Money(0, $lines[0]->getCost()->getCurrency());

        foreach ($lines as $line) {
            $total = $total->add($line->getCost());
        }

        return $total;
    }

    public function addComponent(CostComponentLine $component): self
    {
        $newLines = $this->costComponentLines;
        $newLines[] = $component;

        return new self($newLines);
    }
}
