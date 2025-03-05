<?php

declare(strict_types=1);

namespace App\Exception;

final class UserNotFoundException extends \Exception
{
    public static function withEmail(string $email): self
    {
        return new self(
            \sprintf('Utilisateur non trouvé avec l\'email : %s', [$email])
        );
    }
}
