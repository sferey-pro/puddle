<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Exception;

use App\Module\CostManagement\Domain\Enum\CostItemStatus;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;

final class CostItemImmutableException extends \DomainException
{
    public static function whenStatusIs(CostItemId $id, CostItemStatus $status, string $operation): self
    {
        return new self(
            \sprintf(
                'Operation "%s" is not allowed for CostItem ID "%s" when status is "%s".',
                $operation,
                $id,
                $status->value
            )
        );
    }
}
