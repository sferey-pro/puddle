<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Exception;

final class InvalidTargetAmountException extends \DomainException
{
    public function __construct(string $message = 'Invalid target amount for CostItem.')
    {
        parent::__construct($message);
    }

    public static function mustBePositive(): self
    {
        return new self('Target amount must be a positive value.');
    }
}
