<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Exception;

use App\Module\CostManagement\Domain\ValueObject\CostItemId;

final class CostItemAlreadyArchivedException extends \DomainException
{
    public static function withId(CostItemId $id): self
    {
        return new self(\sprintf('CostItem with ID "%s" is already archived.', $id));
    }
}
