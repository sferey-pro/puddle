<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Query;

use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsQueryHandler;

#[AsQueryHandler]
final readonly class UserExistsQueryHandler
{
    public function __construct(private UserRepositoryInterface $repository)
    {
    }

    public function __invoke(UserExistsQuery $query): bool
    {
        $user = null;

        if (null !== $query->id) {
            $user = $this->repository->ofId($query->id);
        }

        return null !== $user;
    }
}
