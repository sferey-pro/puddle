<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Specification;

use App\Module\UserManagement\Domain\Enum\UserStatus;

final class UserIsActiveSpecification extends UserHasStatusSpecification
{
    public function __construct()
    {
        parent::__construct(UserStatus::ACTIVE);
    }
}
