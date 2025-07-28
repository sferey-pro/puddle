<?php

namespace Account\Registration\Application\Notifier;

use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Notification\SmsNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\SmsRecipientInterface;

final class WelcomeSmsNotification extends Notification implements SmsNotificationInterface
{
    public function __construct(
        private readonly string $otpCode,
        private readonly string $phoneNumber
    ) {
        parent::__construct('Welcome!');
    }

    public function asSmsMessage(SmsRecipientInterface $recipient, ?string $transport = null): ?SmsMessage
    {
        $message = sprintf(
            "Welcome to Puddle! Your verification code is: %s\nValid for 15 minutes.",
            $this->otpCode
        );

        return new SmsMessage($recipient->getPhone(), $message);
    }
}
