<?php

declare(strict_types=1);

namespace App\Module\User\Application\Query;

use App\Module\User\Domain\Repository\UserRepositoryInterface;
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

        if (null !== $query->identifier) {
            $user = $this->repository->ofIdentifier($query->identifier);
        }

        return null !== $user;
    }
}
