<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Query;

use App\Module\Auth\Domain\UserAccount;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Application\Query\QueryInterface;

/**
 * @implements QueryInterface<UserAccount>
 */
final readonly class FindUserQuery implements QueryInterface
{
    public function __construct(
        public ?UserId $id = null,
    ) {
    }
}
