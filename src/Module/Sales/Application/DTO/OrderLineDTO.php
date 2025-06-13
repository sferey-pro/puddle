<?php

declare(strict_types=1);

namespace App\Module\Sales\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Sequentially;

class OrderLineDTO
{
    #[Sequentially(
        constraints: [
            new Assert\NotBlank(),
            new Assert\Uuid(),
        ]
    )]
    public string $productId;

    #[Sequentially(
        constraints: [
            new Assert\NotBlank(),
            new Assert\Positive(),
        ]
    )]
    public int $quantity;
}
