<?php

declare(strict_types=1);

namespace Authentication\Application\Command;

use Kernel\Application\Message\CommandInterface;

final readonly class VerifyOTP implements CommandInterface
{
    public function __construct(
        public string $code,
        public string $phoneNumber,
        public string $ipAddress,
        public ?string $userAgent = null
    ) {}
}
