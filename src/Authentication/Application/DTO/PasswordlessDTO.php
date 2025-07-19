<?php

declare(strict_types=1);

namespace Authentication\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class PasswordlessDTO
{
    #[Assert\NotBlank(message: 'Please enter an email or phone number!')]
    #[Assert\Length(min: 3, max: 100)]
    public ?string $identifier;

    public function __construct(
        ?string $identifier = null,
    ) {
        $this->identifier = $identifier;
    }
}
