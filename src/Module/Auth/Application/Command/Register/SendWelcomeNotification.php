<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\Register;

use App\Core\Application\Command\CommandInterface;
use App\Module\Auth\Domain\Notification\NotificationChannel;
use App\Module\SharedContext\Domain\ValueObject\UserId;

final readonly class SendWelcomeNotification implements CommandInterface
{
    public function __construct(
        public UserId $userId,
        public NotificationChannel $channel
    ) {}
}
