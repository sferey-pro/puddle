<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Notifier;

use App\Module\Auth\Domain\Notification\OtpNotifierInterface;
use App\Module\Auth\Domain\ValueObject\PhoneIdentity;
use App\Module\Auth\Domain\ValueObject\UserIdentity;
use Symfony\Component\Notifier\NotifierInterface;

final readonly class EmailOtpNotifier implements OtpNotifierInterface
{
    public function __construct(NotifierInterface $notifier)
    {
    }

    public function notify(UserIdentity $identity, string $plainOtpCode): void
    {
        if (!$this->supports($identity)) {
            return;
        }

        $message = sprintf('Votre code de connexion Puddle est : %s. Il expire dans 5 minutes.', $plainOtpCode);

        $this->notifier->send($identity->value(), $message);
    }

    public function supports(UserIdentity $identity): bool
    {
        return $identity instanceof PhoneIdentity;
    }
}
