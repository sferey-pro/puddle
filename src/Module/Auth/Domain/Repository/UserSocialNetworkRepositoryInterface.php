<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Repository;

use App\Module\Auth\Domain\Model\UserSocialNetwork;

interface UserSocialNetworkRepositoryInterface
{
    public function add(UserSocialNetwork $model): void;

    public function remove(UserSocialNetwork $model): void;
}
