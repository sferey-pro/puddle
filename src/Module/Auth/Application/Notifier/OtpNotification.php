<?php
// src/Module/Auth/Application/Notifier/OtpNotification.php
declare(strict_types=1);

namespace App\Module\Auth\Application\Notifier;

use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Notification\SmsNotificationInterface;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Notifier\Recipient\RecipientInterface;
use Symfony\Component\Notifier\Recipient\SmsRecipientInterface;

final class OtpNotification extends Notification implements EmailNotificationInterface, SmsNotificationInterface
{
    public function __construct(
        private readonly string $plainOtpCode
    ) {
        parent::__construct('Votre code de connexion Puddle');
    }

    /**
     * Construit le message au format SMS en utilisant la classe générique.
     * Le transport FreeMobile saura comment gérer cet objet.
     */
    public function asSmsMessage(SmsRecipientInterface $recipient, ?string $transport = null): ?SmsMessage
    {
        $message = sprintf(
            'Votre code de connexion Puddle est : %s. Il expire dans 5 minutes.',
            $this->plainOtpCode
        );

        // On crée une instance standard de SmsMessage. C'est tout.
        return new SmsMessage($recipient->getPhone(), $message);
    }

    /**
     * La version email reste inchangée.
     */
    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): ?EmailMessage
    {
        $email = EmailMessage::fromNotification($this, $recipient, $transport);

        $email->getMessage()
            ->htmlTemplate('@Auth/emails/otp_code.html.twig')
            ->context(['otp_code' => $this->plainOtpCode]);

        return $email;
    }

    public function getChannels(RecipientInterface $recipient): array
    {
        if ($recipient instanceof SmsRecipientInterface) {
            return ['sms'];
        }
        if ($recipient instanceof EmailRecipientInterface) {
            return ['email'];
        }
        return [];
    }
}
