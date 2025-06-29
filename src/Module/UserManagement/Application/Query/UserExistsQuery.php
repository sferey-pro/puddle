<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Query;

use App\Core\Application\Query\QueryInterface;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * @implements QueryInterface<bool>
 */
final readonly class UserExistsQuery implements QueryInterface
{
    public function __construct(
        public ?UserId $id = null,
    ) {
    }
}
