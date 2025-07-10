<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Service;

use App\Module\Auth\Domain\UserAccount;

interface PasswordHasherInterface
{
    public function hash(UserAccount $user, #[\SensitiveParameter] string $plainPassword): string;

    public function verify(UserAccount $user,  #[\SensitiveParameter] string $plainPassword): bool;
}
