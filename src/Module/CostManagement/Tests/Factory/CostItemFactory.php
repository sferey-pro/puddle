<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Tests\Factory;

use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\Enum\CostItemStatus;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\CostManagement\Domain\ValueObject\CostItemName;
use App\Module\CostManagement\Domain\ValueObject\CoveragePeriod;
use App\Module\SharedContext\Domain\ValueObject\Money;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use function Zenstruck\Foundry\faker;

/**
 * @extends PersistentProxyObjectFactory<CostItem>
 */
final class CostItemFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        $targetAmountCents = faker()->numberBetween(50000, 300000); // 500 - 3000 EUR

        // Generate a start date within the last month or next month
        $mutableStartDate = faker()->dateTimeBetween('-1 month', '+1 month');
        $immutableStartDate = \DateTimeImmutable::createFromMutable($mutableStartDate);

        // End date is between 15 to 60 days after the start date
        $mutableEndDate = (clone $mutableStartDate)->modify('+'.faker()->numberBetween(15, 60).' days');
        $immutableEndDate = \DateTimeImmutable::createFromMutable($mutableEndDate);

        return [
            'id' => CostItemId::generate(),
            'name' => new CostItemName(faker()->words(3, true).' - Frais de Test'),
            'targetAmount' => Money::fromFloat($targetAmountCents),
            'currentAmount' => Money::zero(), // Default for a new item
            'coveragePeriod' => CoveragePeriod::create($immutableStartDate, $immutableEndDate),
            'status' => CostItemStatus::ACTIVE,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static // En v2, le type de retour est `static` ou `self`
    {
        return $this;
    }

    public static function class(): string // Changement de protected static function getClass(): string
    {
        return CostItem::class;
    }

    public function covered(): self
    {
        return $this->with(static function (array $attributes): array {
            $targetAmount = $attributes['targetAmount'] ?? Money::fromFloat(100.000); // Valeur par défaut si non définie
            if (!$targetAmount instanceof Money && isset($attributes['targetAmount']['amount'], $attributes['targetAmount']['currency'])) {
                // Au cas où $attributes['targetAmount'] serait un array (possible avec certains setups de states)
                $targetAmount = new Money($attributes['targetAmount']['amount'], $attributes['targetAmount']['currency']);
            } elseif (!$targetAmount instanceof Money) {
                $targetAmount = Money::fromFloat(100.000); // Fallback robuste
            }

            return [
                'currentAmount' => $targetAmount, // Couvrir entièrement
                'status' => CostItemStatus::FULLY_COVERED,
            ];
        });
    }

    public function archived(): self
    {
        return $this->with(['status' => CostItemStatus::ARCHIVED]);
    }

    public function withSpecificAmountCovered(int $amountCents, string $currency = 'EUR'): self
    {
        return $this->with(['currentAmount' => new Money($amountCents, $currency)]);
    }

    public function forNextMonth(): self
    {
        return $this->with(static function (): array {
            $startDate = (new \DateTimeImmutable('first day of next month'))->setTime(0, 0, 0);
            $endDate = (new \DateTimeImmutable('last day of next month'))->setTime(23, 59, 59);

            return [
                'coveragePeriod' => CoveragePeriod::create($startDate, $endDate),
            ];
        });
    }

    public function forCurrentMonth(): self
    {
        return $this->with(static function (): array {
            $startDate = (new \DateTimeImmutable('first day of this month'))->setTime(0, 0, 0);
            $endDate = (new \DateTimeImmutable('last day of this month'))->setTime(23, 59, 59);

            return [
                'coveragePeriod' => CoveragePeriod::create($startDate, $endDate),
            ];
        });
    }

    public function forPreviousMonth(): self
    {
        return $this->with(static function (): array {
            $startDate = (new \DateTimeImmutable('first day of last month'))->setTime(0, 0, 0);
            $endDate = (new \DateTimeImmutable('last day of last month'))->setTime(23, 59, 59);

            return [
                'coveragePeriod' => CoveragePeriod::create($startDate, $endDate),
            ];
        });
    }
}
