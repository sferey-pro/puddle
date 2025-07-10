<?php


declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Symfony\Security;

use App\Core\Application\Query\QueryBusInterface;
use App\Module\Auth\Application\Query\FindUserByIdentifierQuery;
use App\Module\Auth\Domain\Exception\UserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final readonly class UserProvider implements UserProviderInterface
{
    public function __construct(
        private QueryBusInterface $queryBus,
    ) {
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return $class === SecurityUser::class;
    }


    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        try {
            $userAccount = $this->queryBus->ask(new FindUserByIdentifierQuery($identifier));
        } catch (\TypeError $e) {
            throw UserException::notFound();
        }

        return new SecurityUser(
            $userAccount->id,
            $userAccount->identifier,
            $userAccount->password,
            $userAccount->roles->toSecurityRoles()
        );
    }
}
