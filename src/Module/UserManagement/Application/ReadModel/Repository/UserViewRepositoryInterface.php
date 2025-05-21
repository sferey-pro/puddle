<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\ReadModel\Repository;

use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Application\ReadModel\UserView;

interface UserViewRepositoryInterface
{
    public function findById(UserId $identifier): ?UserView;

    public function findAll(): array; // Retourne un tableau de UserView

    public function save(UserView $user, bool $flush = false): void;
}
