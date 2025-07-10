<?php

namespace Account\Registration\Domain\Repository;

use Account\Core\Domain\Account;

interface RegistrationRepositoryInterface
{
    public function save(Account $account): void;
}
