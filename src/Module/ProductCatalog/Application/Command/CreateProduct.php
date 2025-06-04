<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Application\Command;

use App\Module\ProductCatalog\Application\DTO\CreateProductDTO;
use App\Shared\Application\Command\CommandInterface;

final readonly class CreateProduct implements CommandInterface
{
    public function __construct(
        public CreateProductDTO $dto,
    ) {
    }
}
