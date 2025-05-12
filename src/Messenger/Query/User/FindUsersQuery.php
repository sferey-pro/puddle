<?php

declare(strict_types=1);

namespace App\Messenger\Query\User;

use App\Common\Query\QueryInterface;

final readonly class FindUsersQuery implements QueryInterface
{
    public function __construct(
        public ?int $page = null,
        public ?int $itemsPerPage = null,
    ) {
    }
}
