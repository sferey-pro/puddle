<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Repository;

use App\Module\Auth\Domain\Model\UserLogin;

interface UserLoginRepositoryInterface
{
    public function add(UserLogin $model): void;

    public function remove(UserLogin $model): void;

    public function save(UserLogin $model, bool $flush = false): void;
}
