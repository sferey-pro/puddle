<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserDTO
{
    #[Assert\NotBlank(message: 'Please enter an email or phone number!')]
    public ?string $identifier;

    #[Assert\IsTrue(message: 'You should agree to our terms.')]
    public ?bool $agreeTerms;

    public function __construct(
        ?string $identifier = null,
        ?bool $agreeTerms = null,
    ) {
        $this->identifier = $identifier;
        $this->agreeTerms = $agreeTerms;
    }
}
