<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Event;

use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;

final class UserRegistered
{
    public function __construct(
        public UserId $identifier,
        public Email $email,
    ) {
    }
}
