<?php

declare(strict_types=1);

namespace App\DTO;

class CreateUserDTO extends AbstractDTO
{
    public readonly ?string $email;

    public function __construct(
        ?string $email = null,
    ) {
        $this->email = $email;
    }
}
