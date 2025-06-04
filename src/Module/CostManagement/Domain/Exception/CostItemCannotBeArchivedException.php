<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Exception;

use App\Module\CostManagement\Domain\ValueObject\CostItemId;

final class CostItemCannotBeArchivedException extends \DomainException
{
    public static function forId(CostItemId $id, string $reason = 'The CostItem cannot be archived due to current business rules.'): self
    {
        return new self(sprintf('CostItem with ID "%s": %s', $id, $reason));
    }
}
