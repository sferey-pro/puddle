<?php

declare(strict_types=1);

namespace Kernel\Domain\Aggregate;

abstract class AggregateRoot
{
    use RecordsDomainEvents;
}
