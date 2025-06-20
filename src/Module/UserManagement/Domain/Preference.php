<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain;

use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Event\DomainEventTrait;

final class Preference extends AggregateRoot
{
    use DomainEventTrait;
}
