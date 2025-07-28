<?php

namespace Kernel\Domain\Contract\Repository;

interface Paginatable
{
    public function paginate(int $page, int $perPage): PaginationResult;
    public function count(array $criteria = []): int;
}