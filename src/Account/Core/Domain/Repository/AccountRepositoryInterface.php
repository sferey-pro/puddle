<?php

namespace Account\Core\Domain\Repository;

use Account\Core\Domain\Model\Account;
use SharedKernel\Domain\ValueObject\Identity\UserId;

interface AccountRepositoryInterface
{
    // ========== CRUD ==========
    public function save(Account $account): void;
    public function remove(Account $account): void;

}
