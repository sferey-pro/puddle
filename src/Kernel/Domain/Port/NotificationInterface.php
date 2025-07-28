<?php

declare(strict_types=1);

namespace Kernel\Domain\Port;

/**
 * Port pour les notifications (Domain layer)
 */
interface NotificationInterface
{
    public function send(NotificationMessage $message): void;
    public function supports(string $channel): bool;
}
