<?php

declare(strict_types=1);

namespace App\Module\SharedContext\Domain\ValueObject;

use App\Shared\Domain\ValueObject\AggregateRootId;

final class UserId implements \Stringable
{
    use AggregateRootId;
}
