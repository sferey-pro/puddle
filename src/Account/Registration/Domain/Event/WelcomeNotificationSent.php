<?php

declare(strict_types=1);

namespace Account\Registration\Domain\Event;

use Kernel\Domain\Event\DomainEvent;
use SharedKernel\Domain\ValueObject\Identity\UserId;

final readonly class WelcomeNotificationSent extends DomainEvent
{
    /**
     * @param UserId $userId L'ID du compte concerné.
     * @param string $channel Le type de notification.
     */
    public function __construct(
        private(set) UserId $userId,
        private(set) string $channel
    ) {
        parent::__construct($userId);
    }

    /**
     * {@inheritDoc}
     */
    public static function eventName(): string
    {
        return 'account_registration.welcome_notification.sent';
    }
}
