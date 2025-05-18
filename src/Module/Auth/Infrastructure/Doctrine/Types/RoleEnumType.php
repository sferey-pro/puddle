<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Doctrine\Types;

use App\Module\Auth\Domain\Enum\Role;

class RoleEnumType extends AbstractEnumType
{
    public const NAME = 'role';

    public static function getEnumsClass(): string
    {
        return Role::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
