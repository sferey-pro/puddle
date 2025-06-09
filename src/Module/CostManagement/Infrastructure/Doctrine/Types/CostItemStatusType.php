<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Infrastructure\Doctrine\Types;

use App\Module\CostManagement\Domain\Enum\CostItemStatus;
use App\Shared\Infrastructure\Doctrine\Types\AbstractEnumType;

class CostItemStatusType extends AbstractEnumType
{
    public const NAME = 'cost_item_status_enum';

    public static function getEnumsClass(): string
    {
        return CostItemStatus::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
