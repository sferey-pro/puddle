<?php

declare(strict_types=1);

namespace Account\Registration\Application\Command;


final readonly class SendWelcomeNotification implements CommandInterface
{
    public function __construct(
        public UserId $userId,
        public NotificationChannel $channel
    ) {}
}
