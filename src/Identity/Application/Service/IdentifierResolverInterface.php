<?php

declare(strict_types=1);

namespace Identity\Application\Service;

use Kernel\Domain\Result;

interface NotificationChannelResolverInterface
{
    public static function resolve(string $identifier): Result;
}
