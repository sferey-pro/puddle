<?php

declare(strict_types=1);

namespace Account\Registration\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserDTO
{
    #[Assert\NotBlank(message: 'Please enter an email or phone number!')]
    public ?string $identifier;

    public function __construct(
        ?string $identifier = null,
    ) {
        $this->identifier = $identifier;
    }
}
