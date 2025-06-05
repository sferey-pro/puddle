<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Exception;

use App\Module\CostManagement\Domain\ValueObject\CostItemId;

final class CostItemNotArchivedException extends \DomainException
{
    public static function withId(CostItemId $id): self
    {
        return new self(\sprintf('CostItem with ID "%s" is not archived, cannot reactivate.', $id));
    }
}
