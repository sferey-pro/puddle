<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Exception;

use App\Module\SharedContext\Domain\ValueObject\UserId;

final class UserNotFoundException extends \Exception
{
    public static function withEmail(string $email): self
    {
        return new self(
            \sprintf('User not found with email : %s', [$email])
        );
    }

    public static function withUserId(UserId $identifier): self
    {
        return new self(
            \sprintf('User not found with identifier : %s', [$identifier])
        );
    }
}
