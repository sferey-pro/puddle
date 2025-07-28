<?php

declare(strict_types=1);

namespace Account\Registration\Application\Command;

use Identity\Domain\ValueObject\Identifier;
use Kernel\Application\Message\CommandInterface;
use SharedKernel\Domain\ValueObject\Identity\UserId;

final readonly class SendWelcomeNotification implements CommandInterface
{
    public function __construct(
        public UserId $userId,
        public Identifier $identifier
    ) {}
}
