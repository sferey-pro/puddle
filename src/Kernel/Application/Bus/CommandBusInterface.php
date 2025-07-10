<?php

declare(strict_types=1);

namespace Kernel\Application\Bus;

use Kernel\Application\Message\CommandInterface;

interface CommandBusInterface
{
    /**
     * @template T
     *
     * @param CommandInterface<T> $command
     *
     * @return T
     */
    public function dispatch(CommandInterface $command): mixed;
}
