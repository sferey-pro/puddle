<?php

declare(strict_types=1);

namespace App\Module\SharedContext\Domain\ValueObject;

use Webmozart\Assert\Assert;

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

    public static function zero(): self
    {
        return new self(0);
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
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
