<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Query;

use App\Core\Application\Query\QueryInterface;

/**
 * @implements QueryInterface<UserView[]>
 */
final readonly class ListUsersQuery implements QueryInterface
{
    public function __construct(
        public ?int $page = null,
        public ?int $itemsPerPage = null,
        public ?string $searchTerm = null,
    ) {
    }
}
