<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Specification;

use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\UserManagement\Domain\Exception\EmailAlreadyExistException;

interface UniqueEmailSpecificationInterface
{
    /**
     * @throws EmailAlreadyExistException
     */
    public function isUnique(Email $email): bool;
}
