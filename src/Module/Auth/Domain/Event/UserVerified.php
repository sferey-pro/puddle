<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Event;

use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\DomainEventInterface;

final class UserVerified extends DomainEvent implements DomainEventInterface
{
    public function __construct(
        private UserId $id,
        private bool $verified,
    ) {
        parent::__construct();
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function verified(): bool
    {
        return $this->verified;
    }
}
