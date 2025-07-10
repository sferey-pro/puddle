<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Symfony\Security;

use App\Module\Auth\Domain\Service\PasswordHasherInterface;
use App\Module\Auth\Domain\UserAccount;
use App\Module\Auth\Domain\ValueObject\EmailIdentity;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class UserPasswordHasher implements PasswordHasherInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    #[\Override]
    public function hash(UserAccount $user, string $password): string
    {
        $securityUser = SecurityUser::create($user->email, $password, $user->roles);
        return $this->passwordHasher->hashPassword($securityUser, $password);
    }

    #[\Override]
    public function verify(UserAccount $user, string $password): bool
    {
        $securityUser = SecurityUser::create($user->email, $password, $user->roles);
        return $this->passwordHasher->isPasswordValid($securityUser, $password);
    }
}
