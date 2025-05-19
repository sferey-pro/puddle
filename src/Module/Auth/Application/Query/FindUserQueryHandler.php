<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Query;

use App\Module\Auth\Domain\Exception\UserNotFoundException;
use App\Module\Auth\Domain\User;
use App\Module\Auth\Infrastructure\Doctrine\Repository\UserRepository;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsQueryHandler;

#[AsQueryHandler]
final readonly class FindUserQueryHandler
{
    public function __construct(private UserRepository $repository)
    {
    }

    public function __invoke(FindUserQuery $query): User
    {
        $user = null;

        if (null !== $query->id) {
            $user = $this->repository->ofId($query->id);
        } elseif (null !== $query->identifier) {
            $user = $this->repository->ofIdentifier($query->identifier);
        }

        if (null === $user) {
            throw UserNotFoundException::withUserId($query->identifier);
        }

        return $user;
    }
}
