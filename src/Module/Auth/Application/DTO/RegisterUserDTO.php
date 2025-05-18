<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\DTO;

use App\Module\Auth\Domain\Model\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Sequentially;

#[UniqueEntity(
    'email',
    entityClass: User::class,
    repositoryMethod: 'ofNativeEmail'
)]
class RegisterUserDTO
{
    #[Sequentially([
        new NotBlank(message: 'Please enter an email!'),
        new Email(),
    ])]
    public ?string $email;

    #[Sequentially([
        new NotBlank(
            message: 'Please enter a password',
        ),
        new Length(
            min: 6,
            max: 4096,
            // max length allowed by Symfony for security reasons
            minMessage: 'Your password should be at least {{ limit }} characters',
        )]
    )]
    public ?string $plainPassword;

    #[IsTrue(message: 'You should agree to our terms.')]
    public ?bool $agreeTerms;

    public function __construct(
        ?string $email = null,
        ?string $plainPassword = null,
        ?bool $agreeTerms = null,
    ) {
        $this->email = $email;
        $this->plainPassword = $plainPassword;
        $this->agreeTerms = $agreeTerms;
    }
}
