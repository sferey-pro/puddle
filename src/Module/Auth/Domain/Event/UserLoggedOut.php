<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Event;

use App\Module\Shared\Domain\ValueObject\UserId;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\DomainEventInterface;

final class UserLoggedOut extends DomainEvent implements DomainEventInterface
{
    public function __construct(
        private UserId $identifier,
    ) {
        parent::__construct();
    }

    public function identifier(): UserId
    {
        return $this->identifier;
    }
}
