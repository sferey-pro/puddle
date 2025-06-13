<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Exception;

use App\Module\SharedContext\Domain\ValueObject\UserId;

final class UserNotFoundException extends \Exception
{
    public static function withEmail(string $email): self
    {
        return new self(
            \sprintf('Utilisateur non trouvé avec l\'email : %s', [$email])
        );
    }

    public static function withUserId(UserId $id): self
    {
        return new self(
            \sprintf('Utilisateur non trouvé avec l\'identifiant : %s', [$id->value])
        );
    }
}
