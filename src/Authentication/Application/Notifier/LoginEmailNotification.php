<?php

declare(strict_types=1);

namespace Authentication\Application\Notifier;

use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkDetails;

/**
 * Notification email pour connexion par magic link.
 */
final class LoginEmailNotification extends Notification implements EmailNotificationInterface
{
    public function __construct(
        private readonly LoginLinkDetails $loginLinkDetails,
        private readonly string $userEmail,
        private readonly string $ipAddress
    ) {
        parent::__construct('Login to your account');
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): ?EmailMessage
    {
        $email = EmailMessage::fromNotification($this, $recipient, $transport);

        $email->getMessage()
            ->htmlTemplate('@Authentication/emails/login_magic_link.html.twig')
            ->context([
                'magic_link_url' => $this->loginLinkDetails->getUrl(),
                'user_email' => $this->userEmail,
                'ip_address' => $this->ipAddress,
                'expires_at' => $this->loginLinkDetails->getExpiresAt()->format('g:i A'),
                'expires_in_minutes' => $this->calculateMinutesUntilExpiry(),
            ]);

        return $email;
    }

    private function calculateMinutesUntilExpiry(): int
    {
        $now = new \DateTime();
        $diff = $this->loginLinkDetails->getExpiresAt()->diff($now);
        return ($diff->h * 60) + $diff->i;
    }

    public function getImportance(): string
    {
        return self::IMPORTANCE_HIGH;
    }
}
