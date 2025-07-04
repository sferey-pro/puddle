<?php

declare(strict_types=1);

namespace App\Core\Application\Security;

use App\Module\Auth\Domain\ValueObject\Password;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


interface PasswordFactoryInterface
{
    public function createFromPlain(
        PasswordAuthenticatedUserInterface $user,
        string $plainPassword,
    ): Password;
}
