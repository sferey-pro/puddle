<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Notifier;

use App\Module\Auth\Domain\ValueObject\LoginLinkDetails;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;

class CustomLoginLinkNotification extends LoginLinkNotification
{
    public function __construct(
        private LoginLinkDetails $loginLinkDetails,
        private string $subject,
    ) {
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): ?EmailMessage
    {
        $email = NotificationEmail::asPublicEmail()
            ->to($recipient->getEmail())
            ->subject($this->subject)
            ->htmlTemplate('@Auth/emails/security/custom_login_link_email.html.twig')
            ->action('Sign in', $this->loginLinkDetails->url())
        ;

        return new EmailMessage($email);
    }
}
