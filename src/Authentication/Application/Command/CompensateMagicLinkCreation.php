<?php

declare(strict_types=1);

namespace Authentication\Application\Command;

use Identity\Domain\ValueObject\EmailIdentity;
use Identity\Domain\ValueObject\Identifier;
use Kernel\Application\Message\CommandInterface;
use SharedKernel\Domain\ValueObject\Identity\UserId;

final class CompensateMagicLinkCreation implements CommandInterface
{
    public function __construct(
        public UserId $userId,
        public Identifier $identifier
    ) {
    }
}
