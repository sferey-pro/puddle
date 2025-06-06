<?php

declare(strict_types=1);

namespace App\Module\SharedContext\Domain\Exception;

/**
 * Exception levée lorsqu'une opération sur un objet Money échoue en raison de règles
 * fondamentales sur la monnaie (ex: opérations sur des devises différentes, montants invalides).
 */
final class InvalidMoneyException extends \InvalidArgumentException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function currencyMismatch(string $expected, string $actual): self
    {
        return new self(
            \sprintf('Currency mismatch. Expected "%s" but got "%s".', $expected, $actual)
        );
    }

    public static function amountMustBePositive(): self
    {
        return new self('Money amount for this operation must be positive.');
    }
}
