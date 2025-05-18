<?php

declare(strict_types=1);

namespace App\Module\User\Domain\Event;

use App\Entity\ValueObject\UserId;
use App\Module\Shared\Domain\ValueObject\Email;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\DomainEventInterface;

final class UserEmailChanged extends DomainEvent implements DomainEventInterface
{
    public function __construct(
        private UserId $identifier,
        private Email $email,
    ) {
        parent::__construct();
    }

    public function identifier(): UserId
    {
        return $this->identifier;
    }

    public function email(): Email
    {
        return $this->email;
    }
}
