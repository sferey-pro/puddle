<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain;

use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Domain\Event\UserCreated;
use App\Module\UserManagement\Domain\ValueObject\Name;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Model\DomainEventTrait;

class User extends AggregateRoot
{
    use DomainEventTrait;

    private function __construct(
        private UserId $identifier,
        private Email $email,
        private ?Name $username = null,
    ) {
    }

    public static function create(
        UserId $identifier,
        Email $email,
        ?Name $username = null,
    ) {
        $user = new self($identifier, $email, $username);

        $user->recordDomainEvent(
            new UserCreated(identifier: $user->identifier(), email: $user->email())
        );

        return $user;
    }

    public function identifier(): UserId
    {
        return $this->identifier;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function username(): ?Name
    {
        return $this->username;
    }
}
