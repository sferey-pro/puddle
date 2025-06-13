<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\Logout;

use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Application\Command\CommandInterface;

final class LogoutUser implements CommandInterface
{
    public function __construct(
        private(set) UserId $id,
    ) {
    }

    public function id(): UserId
    {
        return $this->id;
    }
}
