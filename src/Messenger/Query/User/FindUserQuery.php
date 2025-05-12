<?php

declare(strict_types=1);

namespace App\Messenger\Query\User;

use App\Common\Query\QueryInterface;
use App\Entity\User;
use App\Entity\ValueObject\UserId;

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
