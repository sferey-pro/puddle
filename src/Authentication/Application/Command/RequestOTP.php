<?php

declare(strict_types=1);

namespace Authentication\Application\Command;

use Identity\Domain\ValueObject\PhoneIdentity;
use Kernel\Application\Message\CommandInterface;

final readonly class RequestOTP implements CommandInterface
{
    public function __construct(
        public PhoneIdentity $phoneNumber,
        public string $ipAddress,
        public ?string $userAgent = null
    ) {}
}
