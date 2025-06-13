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
        private UserId $id,
        private Email $email,
        private ?Name $username = null,
    ) {
    }

    public static function create(
        UserId $id,
        Email $email,
        ?Name $username = null,
    ) {
        $user = new self($id, $email, $username);

        $user->recordDomainEvent(
            new UserCreated(id: $user->id(), email: $user->email())
        );

        return $user;
    }

    public function id(): UserId
    {
        return $this->id;
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
