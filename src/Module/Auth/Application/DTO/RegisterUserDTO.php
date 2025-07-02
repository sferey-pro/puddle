<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\DTO;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Sequentially;

class RegisterUserDTO
{
    #[Sequentially([
        new NotBlank(message: 'Please enter an email!'),
        new Email(),
    ])]
    public ?string $email;

    #[IsTrue(message: 'You should agree to our terms.')]
    public ?bool $agreeTerms;

    public function __construct(
        ?string $email = null,
        ?bool $agreeTerms = null,
    ) {
        $this->email = $email;
        $this->agreeTerms = $agreeTerms;
    }
}
