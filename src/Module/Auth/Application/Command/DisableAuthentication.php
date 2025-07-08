<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Core\Application\Command\CommandInterface;
use App\Module\SharedContext\Domain\ValueObject\UserId;

final class DisableAuthentication implements CommandInterface
{
    public function __construct(
        public UserId $userId,
        public string $reason
    ) {
    }

}
