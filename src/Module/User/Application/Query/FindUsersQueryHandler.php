<?php

declare(strict_types=1);

namespace App\Module\User\Application\Query;

use App\Module\User\Domain\Repository\UserRepositoryInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsQueryHandler;

#[AsQueryHandler]
final readonly class FindUsersQueryHandler
{
    public function __construct(
        private UserRepositoryInterface $repository,
    ) {
    }

    public function __invoke(FindUsersQuery $query): UserRepositoryInterface
    {
        $repository = $this->repository;

        if (null !== $query->page && null !== $query->itemsPerPage) {
            $repository = $repository->withPagination($query->page, $query->itemsPerPage);
        }

        return $repository;
    }
}
