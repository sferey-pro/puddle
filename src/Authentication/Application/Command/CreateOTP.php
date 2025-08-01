<?php

declare(strict_types=1);

namespace Authentication\Application\Command;

use Identity\Domain\ValueObject\Identifier;
use Kernel\Application\Message\CommandInterface;
use SharedKernel\Domain\ValueObject\Identity\UserId;


final class CreateOTP implements CommandInterface
{
    public function __construct(
        public UserId $userId,
        public Identifier $identifier,
        public int $lifetime = 300,
    ) {
    }
}
