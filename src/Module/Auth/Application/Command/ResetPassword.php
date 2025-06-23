<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Shared\Application\Command\CommandInterface;

final readonly class ResetPassword implements CommandInterface
{
    public function __construct(
        public string $token,
        #[\SensitiveParameter] public string $newPassword,
    ) {
    }
}
