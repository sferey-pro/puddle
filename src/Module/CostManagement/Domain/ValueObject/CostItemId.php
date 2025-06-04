<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\ValueObject;

use App\Shared\Domain\ValueObject\AggregateRootId;

final class CostItemId implements \Stringable
{
    use AggregateRootId;
}
