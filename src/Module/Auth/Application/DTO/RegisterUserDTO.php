<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Sequentially;

class RegisterUserDTO
{
    #[Sequentially([
        new Assert\NotBlank(message: 'Please enter an email!'),
        new Assert\Email(),
    ])]
    public ?string $email;

    #[Assert\IsTrue(message: 'You should agree to our terms.')]
    public ?bool $agreeTerms;

    public function __construct(
        ?string $email = null,
        ?bool $agreeTerms = null,
    ) {
        $this->email = $email;
        $this->agreeTerms = $agreeTerms;
    }
}
