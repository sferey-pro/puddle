<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Event;

use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\DomainEventInterface;

final class UserRegistered extends DomainEvent implements DomainEventInterface
{
    public function __construct(
        private UserId $id,
        private Email $email,
    ) {
        parent::__construct();
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function email(): Email
    {
        return $this->email;
    }
}
