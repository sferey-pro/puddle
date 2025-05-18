<?php

declare(strict_types=1);

namespace App\Module\User\Application\Query;

use App\Module\User\Domain\Exception\UserNotFoundException;
use App\Module\User\Domain\Model\User;
use App\Module\User\Infrastructure\Doctrine\Repository\UserRepository;
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
