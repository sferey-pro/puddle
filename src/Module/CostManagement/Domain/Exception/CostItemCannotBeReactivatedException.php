<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Exception;

use App\Module\CostManagement\Domain\ValueObject\CostItemId;

final class CostItemCannotBeReactivatedException extends \DomainException
{
    public static function forId(CostItemId $id, string $reason = 'The CostItem cannot be reactivated.'): self
    {
        return new self(\sprintf('CostItem with ID "%s": %s', $id, $reason));
    }

    public static function coveragePeriodEnded(CostItemId $id): self
    {
        return new self(
            \sprintf(
                'CostItem with ID "%s" cannot be reactivated because its coverage period has ended.',
                $id
            )
        );
    }
}
