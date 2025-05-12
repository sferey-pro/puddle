<?php

declare(strict_types=1);

namespace App\Messenger\Query\User;

use App\Entity\User;
use App\Exception\UserNotFoundException;
use App\Messenger\Attribute\AsQueryHandler;
use App\Repository\UserRepository;

#[AsQueryHandler]
final readonly class FindUserQueryHandler
{
    public function __construct(private UserRepository $repository)
    {
    }

    public function __invoke(FindUserQuery $query): User
    {
        $user = null;

        if(null !== $query->id) {
            $user = $this->repository->ofId($query->id);
        } elseif(null !== $query->identifier) {
            $user = $this->repository->ofIdentifier($query->identifier);
        }

        if (null === $user) {
            throw UserNotFoundException::withUserId($query->identifier);
        }

        return $user;
    }
}
