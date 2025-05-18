<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\Security;

use App\Module\Auth\Domain\Model\User;
use App\Module\Auth\Domain\ValueObject\UserLoginId;
use App\Shared\Application\Command\CommandInterface;

final class CreateLoginLink implements CommandInterface
{
    public function __construct(
        public UserLoginId $identifier,
        public User $user,
    ) {
    }
}
