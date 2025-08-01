<?php

namespace Authentication\Application\Command;

use Identity\Domain\ValueObject\Identifier;

final class InitiatePasswordlessRegistration
{
    public function __construct(
        public readonly Identifier $identifier,
        public readonly string $ipAddress,
        public readonly ?string $userAgent = null
    ) {}
}
