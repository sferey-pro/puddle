<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Query;

use App\Module\UserManagement\Domain\Exception\UserNotFoundException;
use App\Module\UserManagement\Domain\Repository\UserRepositoryInterface;
use App\Module\UserManagement\Domain\User;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsQueryHandler;

#[AsQueryHandler]
final readonly class FindUserQueryHandler
{
    public function __construct(private UserRepositoryInterface $repository)
    {
    }

    public function __invoke(FindUserQuery $query): User
    {
        $user = null;

        if (null !== $query->id) {
            $user = $this->repository->ofId($query->id);
        }

        if (null === $user) {
            throw UserNotFoundException::withUserId($query->id);
        }

        return $user;
    }
}
