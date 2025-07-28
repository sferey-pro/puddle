<?php

declare(strict_types=1);

namespace Kernel\Application\Bus;

use Kernel\Application\Message\QueryInterface;

interface QueryBusInterface
{
    /**
     * @template T
     *
     * @param QueryInterface<T> $query
     *
     * @return T
     */
    public function ask(QueryInterface $query): mixed;
}
