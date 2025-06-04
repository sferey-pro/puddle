<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Exception;

use App\Module\CostManagement\Domain\Enum\CostItemStatus;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;

final class CostItemCannotReceiveContributionException extends \DomainException
{
    public static function becauseStatusIs(CostItemId $id, CostItemStatus $status): self
    {
        return new self(
            sprintf(
                'CostItem with ID "%s" cannot receive contribution due to its status: %s.',
                $id,
                $status->value
            )
        );
    }

    public static function alreadyCovered(CostItemId $id): self
    {
        return new self(sprintf('CostItem with ID "%s" is already fully covered.', $id));
    }

    public static function notActive(CostItemId $id): self
    {
        return new self(sprintf('CostItem with ID "%s" is not active and cannot receive contributions.', $id));
    }
}
