<?php

namespace Account\Registration\Application\Notifier;

use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

final class WelcomeEmailNotification extends Notification implements EmailNotificationInterface
{
    public function __construct(
        private readonly string $magicLinkUrl,
        private readonly string $userEmail
    ) {
        parent::__construct('Welcome in Puddle!');
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): ?EmailMessage
    {
        $email = EmailMessage::fromNotification($this, $recipient);

        $email->getMessage()
            ->htmlTemplate('@Account/emails/welcome.html.twig')
            ->context([
                'magic_link_url' => $this->magicLinkUrl,
                'user_email' => $this->userEmail,
                'expires_in_hours' => 24,
            ]);

        return $email;
    }

}
