<?php

declare(strict_types=1);

namespace App\Module\SharedContext\Domain\ValueObject;

use Webmozart\Assert\Assert;

/**
 * Represents a monetary value with a specific currency.
 *
 * Important: For precision, monetary values are often stored as integers
 * representing the smallest currency unit (e.g., cents for EUR/USD).
 * This example will assume the amount is passed as an integer (e.g., 1050 for 10.50 EUR).
 * Adjust if your current implementation uses floats or another representation.
 */
final class Money implements \Stringable
{
    public readonly int $amount; // Stocker en centimes pour éviter les problèmes de floating point
    public readonly string $currency;

    private const DEFAULT_CURRENCY = 'EUR';

    public function __construct(int $amount, ?string $currency = null)
    {
        Assert::greaterThanEq($amount, 0, 'Money amount cannot be negative.');
        $this->amount = $amount;

        $currencyToUse = $currency ?? self::DEFAULT_CURRENCY;
        Assert::length($currencyToUse, 3, 'Currency code must be 3 characters long.');

        $this->currency = mb_strtoupper($currencyToUse);
    }

    public static function zero(?string $currency = null): self
    {
        return new self(0, $currency);
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    private function assertSameCurrency(self $other): void
    {
        Assert::true(
            $this->currency === $other->currency,
            \sprintf('Cannot compare amounts with different currencies. Got %s and %s.', $this->currency, $other->currency)
        );
    }

    public function isGreaterThan(self $other): bool
    {
        $this->assertSameCurrency($other);

        return $this->amount > $other->amount;
    }

    public function isGreaterThanOrEqual(self $other): bool
    {
        $this->assertSameCurrency($other);

        return $this->amount >= $other->amount;
    }

    public function isLessThan(self $other): bool
    {
        $this->assertSameCurrency($other);

        return $this->amount < $other->amount;
    }

    public function isLessThanOrEqual(self $other): bool
    {
        $this->assertSameCurrency($other);


        return $this->amount <= $other->amount;
    }

    public function add(self $other): self
    {
        Assert::eq($this->currency, $other->currency, 'Cannot add money of different currencies.');

        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(self $other): self
    {
        Assert::eq($this->currency, $other->currency, 'Cannot subtract money of different currencies.');

        return new self($this->amount - $other->amount, $this->currency);
    }

    public function multiplyBy(float $factor): self
    {
        return new self((int) round($this->amount * $factor), $this->currency);
    }

    public function isEqualTo(self $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }

    public function __toString(): string
    {
        return \sprintf('%.2f %s', $this->amount / 100, $this->currency);
    }

    public static function fromFloat(float $amount, ?string $currency = null): self
    {
        return new self((int) round($amount * 100), $currency);
    }

    public function toFloat(): float
    {
        return $this->amount / 100;
    }
}
