<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RegisterUserDTO
{
    #[Assert\NotBlank]
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

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        $emailValidator = new Assert\Email();
        $violations = $context->getValidator()->validate($this->identifier, $emailValidator);

        // Si ce n'est pas un email valide, on vérifie si c'est un téléphone
        if (count($violations) > 0) {
            if (!preg_match('/^\+?[1-9]\d{1,14}$/', $this->identifier)) {
                $context->buildViolation('This value is not a valid email or phone number.')
                    ->atPath('identifier')
                    ->addViolation();
            }
        }
    }
}
