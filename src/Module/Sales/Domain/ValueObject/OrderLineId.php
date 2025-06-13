<?php

declare(strict_types=1);

namespace App\Module\Sales\Domain\ValueObject;

use App\Shared\Domain\ValueObject\AggregateRootId;

final class OrderLineId implements \Stringable
{
    use AggregateRootId;
}
