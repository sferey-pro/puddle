<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Exception;

final class InvalidCostItemNameException extends \DomainException
{
    public function __construct(string $message = 'Invalid CostItem name provided.')
    {
        parent::__construct($message);
    }
}
