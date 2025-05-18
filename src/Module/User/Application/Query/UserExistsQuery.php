<?php

declare(strict_types=1);

namespace App\Module\User\Application\Query;

use App\Module\Shared\Domain\ValueObject\UserId;
use App\Shared\Application\Query\QueryInterface;

/**
 * @implements QueryInterface<bool>
 */
final readonly class UserExistsQuery implements QueryInterface
{
    public function __construct(
        public ?UserId $identifier = null,
    ) {
    }
}
