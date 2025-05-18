<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Repository;

use App\Module\Auth\Domain\Model\User;
use App\Module\Shared\Domain\ValueObject\Email;
use App\Module\Shared\Domain\ValueObject\UserId;

interface UserRepositoryInterface
{
    public function save(User $model, bool $flush = false): void;

    public function add(User $model): void;

    public function ofEmail(Email $email): ?User;

    public function ofIdentifier(UserId $identifier): ?User;
}
