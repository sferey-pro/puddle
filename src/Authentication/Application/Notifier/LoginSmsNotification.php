<?php

declare(strict_types=1);

namespace Authentication\Application\Notifier;

use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Notification\SmsNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\SmsRecipientInterface;

/**
 * Notification SMS pour connexion par code OTP.
 */
final class LoginSmsNotification extends Notification implements SmsNotificationInterface
{
    public function __construct(
        private readonly string $otpCode,
        private readonly string $phoneNumber,
        private readonly int $expiresInMinutes = 10
    ) {
        parent::__construct('Login code');
    }

    public function asSmsMessage(SmsRecipientInterface $recipient, ?string $transport = null): ?SmsMessage
    {
        $message = sprintf(
            "Your login code: %s\nExpires in %d minutes.\nDo not share this code.",
            $this->otpCode,
            $this->expiresInMinutes
        );

        return new SmsMessage($recipient->getPhone(), $message);
    }

    public function getImportance(): string
    {
        return self::IMPORTANCE_URGENT;
    }
}
