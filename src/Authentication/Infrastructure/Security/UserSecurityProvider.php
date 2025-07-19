<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Security;

use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserSecurityProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    public function __construct(private readonly AuthRepository $userRepo) {}

    public function refreshUser(UserInterface $user): UserInterface { }

    public function supportsClass(string $class): bool { }

    public function loadUserByIdentifier(string $identifier): UserInterface {

        $user = $this->userRepository->findByIdentifier($identifier);

        if (!$user || !$user instanceof User) {
            throw new UserNotFoundException();
        }

        $securityUser = new UserSecurity();
        $securityUser->setUser($user);

        return $securityUser;
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void { }

}
