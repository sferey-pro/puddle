<?php

declare(strict_types=1);

namespace App\Module\Sales\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Sequentially;

class CreateOrderDTO
{
    #[Sequentially(
        constraints: [
            new Assert\NotBlank,
            new Assert\Uuid,
        ]
    )]
    public string $userId;

    #[Assert\Valid]
    #[Assert\Count(min: 1)]
    /** @var OrderLineDTO[] $orderLines */
    public array $orderLines = [];
}
