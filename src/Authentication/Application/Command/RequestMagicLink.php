<?php

declare(strict_types=1);

namespace Authentication\Application\Command;

use Identity\Domain\ValueObject\EmailIdentity;
use Kernel\Application\Message\CommandInterface;

/**
 * Command pour demander un magic link
 */
final readonly class RequestMagicLink implements CommandInterface
{
    public function __construct(
        public EmailIdentity $email,
        public string $ipAddress,
        public ?string $userAgent = null
    ) {}
}
