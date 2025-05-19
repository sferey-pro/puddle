<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Repository;

use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Domain\User;
use App\Shared\Domain\Repository\RepositoryInterface;

/**
 * @extends RepositoryInterface<User>
 */
interface UserRepositoryInterface extends RepositoryInterface
{
    public function save(User $model, bool $flush = false): void;

    public function add(User $model): void;

    public function remove(User $model): void;

    public function ofIdentifier(UserId $identifier): ?User;
}
