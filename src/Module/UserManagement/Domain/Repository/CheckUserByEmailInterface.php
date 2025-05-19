<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Repository;

use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;

interface CheckUserByEmailInterface
{
    public function existsEmail(Email $email): ?UserId;
}
