<?php

declare(strict_types=1);

namespace App\Module\Sales\Application\Command;

use App\Module\Sales\Application\DTO\CreateOrderDTO;
use App\Shared\Application\Command\CommandInterface;

final class CreateOrder implements CommandInterface
{
    public function __construct(
        public readonly CreateOrderDTO $createOrderDTO,
    ) {
    }
}
