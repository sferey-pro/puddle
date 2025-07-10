<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\Register;

use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use App\Module\Auth\Application\Notifier\SendWelcomeNotification\WelcomeNotifierRegistry;

#[AsCommandHandler]
final readonly class SendWelcomeNotificationHandler
{
    public function __construct(
        private WelcomeNotifierRegistry $registry
    ) {
    }

    public function __invoke(SendWelcomeNotification $command): void
    {
        $this->registry->notify($command->userId, $command->channel);
    }
}
