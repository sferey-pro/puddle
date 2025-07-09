<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Event;

use App\Core\Domain\Event\DomainEvent;
use App\Module\SharedContext\Domain\ValueObject\EmailAddress;
use App\Module\SharedContext\Domain\ValueObject\UserId;

final readonly class UserAccountCreated extends DomainEvent
{
    public function __construct(
        private UserId $aggregateId,
        private EmailAddress $email,
    ) {
        parent::__construct($this->aggregateId);
    }

    public static function eventName(): string
    {
        return 'auth.user_account.created';
    }

    public function userId(): UserId
    {
        return $this->aggregateId;
    }

    public function email(): EmailAddress
    {
        return $this->email;
    }
}
