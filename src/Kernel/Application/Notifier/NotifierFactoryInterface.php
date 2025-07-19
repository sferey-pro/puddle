<?php

declare(strict_types=1);

namespace Kernel\Application\Notifier;

use Identity\Domain\ValueObject\Identifier;
use Symfony\Component\Notifier\Notification\Notification;

interface NotifierFactoryInterface
{
    /**
     * Le type de notification que cette factory gère.
     */
    public function getNotificationType(): string;

    /**
     * Crée la notification appropriée.
     */
    public function createNotification(
        Identifier $identifier,
        array $context = []
    ): Notification;
}
