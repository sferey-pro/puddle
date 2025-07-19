<?php

declare(strict_types=1);

namespace Authentication\Application\Notifier;

use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Notifier\Recipient\SmsRecipientInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;

/**
 * Représente la notification envoyée pour une connexion par "lien magique".
 */
class CustomLoginLinkNotification extends LoginLinkNotification
{
    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): ?EmailMessage
    {
        $emailMessage = parent::asEmailMessage($recipient, $transport);

        /** @var RawMessage $email */
        $email = $emailMessage->getMessage();
        $email->htmlTemplate('@Authentication/emails/custom_login_link_email.html.twig');

        return $emailMessage;
    }

    public function asSmsMessage(SmsRecipientInterface $recipient, ?string $transport = null): ?SmsMessage
    {
        throw new \LogicException('SMS is not a supported channel for login links.');
    }
}
