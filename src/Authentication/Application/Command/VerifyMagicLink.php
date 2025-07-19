<?php

declare(strict_types=1);

namespace Authentication\Application\Command;

use Kernel\Application\Message\CommandInterface;

final readonly class VerifyMagicLink implements CommandInterface
{
    public function __construct(
        public string $token,
        public string $ipAddress,
        public ?string $userAgent = null
    ) {}
}
