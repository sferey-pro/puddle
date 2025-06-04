<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Exception;

final class InvalidCoveragePeriodException extends \DomainException
{
    public function __construct(string $message = 'Invalid coverage period provided.')
    {
        parent::__construct($message);
    }

    public static function endDateBeforeStartDate(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): self
    {
        return new self(
            sprintf(
                'Coverage period end date (%s) cannot be before start date (%s).',
                $endDate->format('Y-m-d'),
                $startDate->format('Y-m-d')
            )
        );
    }
}
