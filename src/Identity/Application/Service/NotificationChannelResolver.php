<?php

declare(strict_types=1);

namespace Identity\Application\Service;

use Account\Core\Domain\Notification\NotificationChannel;
use Identity\Domain\ValueObject\EmailIdentity;
use Identity\Domain\ValueObject\Identifier;
use Identity\Domain\ValueObject\PhoneIdentity;

final class NotificationChannelResolver implements NotificationChannelResolverInterface
{
    public function resolve(Identifier $identifier): NotificationChannel
    {
        return match ($identifier::class) {
            EmailIdentity::class => NotificationChannel::Email,
            PhoneIdentity::class => NotificationChannel::Sms,
            default => throw new \LogicException('Cannot determine notification channel for unknown identifier type.'),
        };
    }
}
