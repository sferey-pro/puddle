<?php

declare(strict_types=1);

namespace Identity\Domain\Repository;

use Identity\Domain\Model\UserIdentity;

interface UserIdentityRepositoryInterface
{
    // ========== CRUD ==========
    public function save(UserIdentity $userIdentity): void;
    public function remove(UserIdentity $userIdentity): void;

}
