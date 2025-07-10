<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\Otp;

use App\Core\Application\Command\CommandInterface;
use App\Module\Auth\Domain\ValueObject\UserIdentity;
use App\Module\SharedContext\Domain\ValueObject\UserId;

final readonly class RequestOtp implements CommandInterface
{
    public function __construct(
        public UserId $userId,
        public UserIdentity $identity,
    ) {
    }
}
