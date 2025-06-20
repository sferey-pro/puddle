<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Query;

use App\Module\UserManagement\Application\ReadModel\Repository\UserViewRepositoryInterface;
use App\Module\UserManagement\Application\ReadModel\UserView;
use App\Module\UserManagement\Domain\Exception\UserException;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsQueryHandler;

#[AsQueryHandler]
final readonly class FindUserQueryHandler
{
    public function __construct(private UserViewRepositoryInterface $repository)
    {
    }

    public function __invoke(FindUserQuery $query): UserView
    {
        $user = null;

        if (null !== $query->id) {
            $user = $this->repository->findById($query->id);
        }

        if (null === $user) {
            throw UserException::notFoundWithId($query->id);
        }

        return $user;
    }
}
