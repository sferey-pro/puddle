<?php

declare(strict_types=1);

namespace Identity\Application\Service;

use Account\Core\Domain\Notification\NotificationChannel;
use Identity\Domain\ValueObject\Identifier;

interface NotificationChannelResolverInterface
{
    public function resolve(Identifier $identifier): NotificationChannel;
}
