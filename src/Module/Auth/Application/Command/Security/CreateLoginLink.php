<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\Security;

use App\Module\Auth\Domain\UserAccount;
use App\Module\Auth\Domain\ValueObject\UserLoginId;
use App\Shared\Application\Command\CommandInterface;

final class CreateLoginLink implements CommandInterface
{
    public function __construct(
        public UserLoginId $id,
        public UserAccount $user,
    ) {
    }
}
