<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Repository;

use App\Module\Auth\Domain\UserAccount;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;

interface UserRepositoryInterface
{
    public function save(UserAccount $model, bool $flush = false): void;

    public function add(UserAccount $model): void;

    public function ofEmail(Email $email): ?UserAccount;

    public function ofIdentifier(UserId $identifier): ?UserAccount;
}
