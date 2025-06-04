<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Exception;

final class InvalidContributionAmountException extends \DomainException
{
    public function __construct(string $message = 'Invalid contribution amount.')
    {
        parent::__construct($message);
    }

    public static function mustBePositive(): self
    {
        return new self('Contribution amount must be a positive value.');
    }
}
