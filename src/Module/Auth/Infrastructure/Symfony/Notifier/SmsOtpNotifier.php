<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Notifier;

use App\Module\Auth\Application\Notifier\OtpNotification;
use App\Module\Auth\Domain\ValueObject\PhoneIdentity;
use App\Module\Auth\Domain\ValueObject\UserIdentity;
use Symfony\Component\Notifier\Recipient\Recipient;

final readonly class SmsOtpNotifier implements OtpNotifierInterface
{
    public function __construct(
        // On injecte le service Notifier principal de Symfony
        private SymfonyNotifierInterface $notifier
    ) {
    }

    public function notify(UserIdentity $identity, string $plainOtpCode): void
    {
        if (!$this->supports($identity)) {
            return;
        }

        // 1. Créer la notification que nous venons de définir.
        $notification = new OtpNotification($plainOtpCode);

        // 2. Créer le destinataire.
        $recipient = new Recipient('', $identity->value()); // Le numéro de téléphone va dans la 2ème partie.

        // 3. Déléguer entièrement l'envoi à Symfony.
        $this->notifier->send($notification, $recipient);
    }

    public function supports(UserIdentity $identity): bool
    {
        return $identity instanceof PhoneIdentity;
    }
}
