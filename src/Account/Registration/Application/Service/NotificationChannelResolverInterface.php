<?php

declare(strict_types=1);

namespace Account\Registration\Application\Service;

use Account\Core\Domain\Notification\NotificationChannel;
use Identity\Domain\ValueObject\Identifier;
use Kernel\Domain\Result;

/**
 * Anti-Corruption Layer : Définit comment le contexte Account/Registration
 * a besoin de résoudre une channel à partir d'un identifiant.
 */
interface NotificationChannelResolverInterface
{
    public function resolve(Identifier $value): NotificationChannel;
}
