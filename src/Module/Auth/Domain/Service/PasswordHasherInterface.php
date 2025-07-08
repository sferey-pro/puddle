<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Service;

interface PasswordHasherInterface
{
    public function hash(#[\SensitiveParameter] string $plainPassword): string;

    public function verify(string $hashedPassword, #[\SensitiveParameter] string $plainPassword): bool;
}
