<?php

namespace Authentication\Application\Notifier;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\SmsNotificationInterface;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Notifier\Recipient\SmsRecipientInterface;

final class PasswordlessLoginNotification extends Notification implements EmailNotificationInterface, SmsNotificationInterface
{
    public function __construct(
        private readonly string $token,
        private readonly int $expiresInMinutes
    ) {
        parent::__construct('Login to your account');
    }

    public function asEmailMessage(EmailRecipientInterface $recipient): ?EmailMessage
    {
        // Token long pour email (magic link)
        $magicLink = sprintf(
            'https://app.com/auth/verify?token=%s',
            $this->token
        );

        $email = (new TemplatedEmail())
            ->to($recipient->getEmail())
            ->subject('Login to your account')
            ->htmlTemplate('emails/magic-link.html.twig')
            ->context([
                'magic_link' => $magicLink,
                'expires_in' => $this->expiresInMinutes
            ]);

        return new EmailMessage($email);
    }

    public function asSmsMessage(SmsRecipientInterface $recipient): ?SmsMessage
    {
        // Code court pour SMS (6 chiffres dérivés du token)
        $otpCode = $this->deriveOTPFromToken($this->token);

        $message = sprintf(
            "Your login code: %s\nExpires in %d minutes.",
            $otpCode,
            $this->expiresInMinutes
        );

        return new SmsMessage($recipient->getPhone(), $message);
    }

    private function deriveOTPFromToken(string $token): string
    {
        // Dérive un code à 6 chiffres du token
        return substr(hash('sha256', $token), 0, 6);
    }
}
