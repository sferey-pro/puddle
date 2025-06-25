<?php

declare(strict_types=1);

namespace App\Module\Sales\Application\Command;

use App\Core\Application\Command\CommandInterface;
use App\Module\Sales\Application\DTO\CreateOrderDTO;

final class CreateOrder implements CommandInterface
{
    public function __construct(
        public readonly CreateOrderDTO $createOrderDTO,
    ) {
    }
}
