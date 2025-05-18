<?php

declare(strict_types=1);

namespace App\Module\User\Application\Query;

use App\Module\Shared\Domain\ValueObject\UserId;
use App\Module\User\Domain\Model\User;
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
