<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain;

use App\Core\Domain\Aggregate\AggregateRoot;
use App\Core\Domain\Event\DomainEventTrait;

final class Preference extends AggregateRoot
{
    use DomainEventTrait;
}
