<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Query;

use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsQueryHandler;
use App\Module\UserManagement\Application\ReadModel\Repository\UserViewRepositoryInterface;

#[AsQueryHandler]
final readonly class ListUsersQueryHandler
{
    public function __construct(
        private UserViewRepositoryInterface $repository,
    ) {
    }

    public function __invoke(ListUsersQuery $query): UserViewRepositoryInterface
    {
        $repository = $this->repository;

        if (null !== $query->page && null !== $query->itemsPerPage) {
            $repository = $repository->withPagination($query->page, $query->itemsPerPage);
        }

        return $repository;
    }
}
