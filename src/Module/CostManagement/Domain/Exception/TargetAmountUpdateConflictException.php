<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Exception;

use App\Module\SharedContext\Domain\ValueObject\Money;

final class TargetAmountUpdateConflictException extends \DomainException
{
    public static function newTargetBelowCurrent(Money $newTarget, Money $currentAmount): self
    {
        return new self(
            sprintf(
                'New target amount (%s %s) cannot be less than the current covered amount (%s %s).',
                $newTarget->toFloat(), // Assuming Money has toFloat() or similar
                $newTarget->getCurrency(),
                $currentAmount->toFloat(),
                $currentAmount->getCurrency()
            )
        );
    }
}
