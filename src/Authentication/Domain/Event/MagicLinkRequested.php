<?php

declare(strict_types=1);

namespace Authentication\Domain\Event;

use Kernel\Domain\Event\DomainEvent;
use SharedKernel\Domain\ValueObject\Identity\UserId;

final class MagicLinkRequested extends DomainEvent
{
    public function __construct(
        UserId $userId,
        private readonly string $email,
        private readonly string $ipAddress
    ) {
        parent::__construct($userId);
    }

    public static function eventName(): string
    {
        return 'authentication.magic_link.requested';
    }
}
