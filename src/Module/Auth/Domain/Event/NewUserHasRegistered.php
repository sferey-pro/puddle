<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Event;

use App\Core\Domain\Event\DomainEvent;
use App\Module\Auth\Domain\ValueObject\LoginLinkDetails;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;

final readonly class NewUserHasRegistered extends DomainEvent
{
    public function __construct(
        private UserId $aggregateId,
        private LoginLinkDetails $loginLinkDetails,
        private ?Email $email = null,
        private ?string $phoneNumber = null,
    ) {
        parent::__construct($this->aggregateId);
    }

    public static function eventName(): string
    {
        return 'auth.user.new_user_registered';
    }

    public function userId(): UserId
    {
        return $this->aggregateId;
    }

    public function email(): ?Email
    {
        return $this->email;
    }

    public function phoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function loginLinkDetails(): LoginLinkDetails
    {
        return $this->loginLinkDetails;
    }
}
