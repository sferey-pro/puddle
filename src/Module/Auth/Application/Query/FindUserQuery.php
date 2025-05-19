<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Query;

use App\Module\Auth\Domain\User;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Application\Query\QueryInterface;

/**
 * @implements QueryInterface<User>
 */
final readonly class FindUserQuery implements QueryInterface
{
    public function __construct(
        public ?int $id = null,
        public ?UserId $identifier = null,
    ) {
    }
}
