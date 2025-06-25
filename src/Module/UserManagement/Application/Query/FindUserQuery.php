<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Query;

use App\Core\Application\Query\QueryInterface;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Application\ReadModel\UserView;

/**
 * @implements QueryInterface<UserView>
 */
final readonly class FindUserQuery implements QueryInterface
{
    public function __construct(
        public ?UserId $id = null,
    ) {
    }
}
