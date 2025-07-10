<?php

declare(strict_types=1);

namespace Account\Registration\Application\Command;


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
