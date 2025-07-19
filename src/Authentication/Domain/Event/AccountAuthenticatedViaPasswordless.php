<?php

declare(strict_types=1);

namespace Authentication\Domain\Event;

final class AccountAuthenticatedViaPasswordless extends DomainEvent
{
    public function __construct(
        UserId $userId,
        private readonly string $method, // 'magic_link' ou 'otp'
        private readonly string $ipAddress
    ) {
        parent::__construct($userId);
    }

    public static function eventName(): string
    {
        return 'authentication.passwordless.success';
    }
}
