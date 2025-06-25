<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Application\Command;

use App\Core\Application\Command\CommandInterface;
use App\Module\ProductCatalog\Application\DTO\CreateProductDTO;

final readonly class CreateProduct implements CommandInterface
{
    public function __construct(
        public CreateProductDTO $dto,
    ) {
    }
}
