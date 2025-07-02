<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Security;

use App\Core\Application\Security\PasswordFactoryInterface;
use App\Module\Auth\Domain\ValueObject\Password;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * Implémentation de la PasswordFactory qui utilise le UserPasswordHasher de Symfony.
 */
final readonly class SymfonyPasswordFactory implements PasswordFactoryInterface
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    public function createFromPlain(
        PasswordAuthenticatedUserInterface $user,
        string $plainPassword,
    ): Password {
        $hashedPassword = $this->userPasswordHasher->hashPassword($user, $plainPassword);

        return new Password($hashedPassword);
    }
}
