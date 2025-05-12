<?php

declare(strict_types=1);

namespace App\Messenger\Query\User;

use App\Messenger\Attribute\AsQueryHandler;
use App\Repository\UserRepository;

#[AsQueryHandler]
final readonly class FindUsersQueryHandler
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function __invoke(FindUsersQuery $query): UserRepository
    {
        $userRepository = $this->userRepository;

        if (null !== $query->page && null !== $query->itemsPerPage) {
            $userRepository = $userRepository->withPagination($query->page, $query->itemsPerPage);
        }

        return $userRepository;
    }
}
