<?php

namespace App\Module\Auth\Application\Command\Otp;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
final readonly class SendOtpNotificationHandler
{
    public function __construct(
        private OtpNotifierSelector $selector
    ) {
    }

    public function __invoke(SendOtpNotification $command): void
    {
        $this->selector->notify($command->identity, $command->plainOtpCode);
    }
}
