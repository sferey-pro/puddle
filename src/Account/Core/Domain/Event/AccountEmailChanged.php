<?php

declare(strict_types=1);

namespace Account\Core\Domain\Event;

use Kernel\Domain\Event\DomainEvent;
use SharedKernel\Domain\ValueObject\Contact\EmailAddress;
use SharedKernel\Domain\ValueObject\Identity\UserId;

final readonly class AccountEmailChanged extends DomainEvent
{
    public function __construct(
        private UserId $aggregateId,
        private EmailAddress $oldEmail,
        private EmailAddress $newEmail,
    ) {
        parent::__construct($aggregateId);
    }

    public static function eventName(): string
    {
        return 'account.email_changed';
    }

    public function userId(): UserId
    {
        return $this->aggregateId;
    }

    public function oldEmail(): EmailAddress
    {
        return $this->oldEmail;
    }

    public function newEmail(): EmailAddress
    {
        return $this->newEmail;
    }
}
