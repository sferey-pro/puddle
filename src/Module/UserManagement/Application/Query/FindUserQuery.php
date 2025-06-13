<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Query;

use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Domain\User;
use App\Shared\Application\Query\QueryInterface;

/**
 * @implements QueryInterface<User>
 */
final readonly class FindUserQuery implements QueryInterface
{
    public function __construct(
        public ?UserId $id = null,
    ) {
    }
}
