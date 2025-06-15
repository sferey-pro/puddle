<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Notifier;

use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;

class CustomLoginLinkNotification extends LoginLinkNotification
{
    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): ?EmailMessage
    {
        $emailMessage = parent::asEmailMessage($recipient, $transport);

        // get the NotificationEmail object and override the template
        $email = $emailMessage->getMessage();
        $email->htmlTemplate('@Auth/emails/security/custom_login_link_email.html.twig');

        return $emailMessage;
    }
}
