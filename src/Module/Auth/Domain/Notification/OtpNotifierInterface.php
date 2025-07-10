<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Notification;

use App\Module\SharedContext\Domain\ValueObject\UserId;

interface OtpNotifierInterface
{
    public function notify(UserId $userId): void;

    /**
     * Retourne le canal géré par cette implémentation.
     * Cette méthode statique sera utilisée pour indexer le service.
     */
    public static function getSupportedChannel(): string;
}
