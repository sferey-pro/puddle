<?php

declare(strict_types=1);

namespace Account\Registration\Domain\Notification;

use SharedKernel\Domain\ValueObject\Identity\UserId;

interface WelcomeNotifierInterface
{
    public function notify(UserId $userId): void;

    /**
     * Retourne le canal géré par cette implémentation.
     * Cette méthode statique sera utilisée pour indexer le service.
     */
    public static function getSupportedChannel(): string;
}
