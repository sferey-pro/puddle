<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Query;

use App\Module\Auth\Domain\Exception\UserNotFoundException;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\UserAccount;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsQueryHandler;

#[AsQueryHandler]
final readonly class FindUserQueryHandler
{
    public function __construct(private UserRepositoryInterface $repository)
    {
    }

    public function __invoke(FindUserQuery $query): UserAccount
    {
        $user = null;

        if (null !== $query->identifier) {
            $user = $this->repository->ofIdentifier($query->identifier);
        }

        if (null === $user) {
            throw UserNotFoundException::withUserId($query->identifier);
        }

        return $user;
    }
}
