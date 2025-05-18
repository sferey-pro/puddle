<?php

declare(strict_types=1);

namespace App\Module\User\Application\DTO;

use App\Module\User\Domain\Model\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Sequentially;

#[UniqueEntity(
    'email',
    entityClass: User::class,
    repositoryMethod: 'ofNativeEmail'
)]
class CreateUserDTO
{
    #[Sequentially([
        new NotBlank(message: 'Please enter an email!'),
        new Email(),
    ])]
    public ?string $email;

    #[NotBlank(message: 'Please enter a username')]
    public ?string $username;

    public function __construct(
        ?string $email = null,
        ?string $username = null,
    ) {
        $this->email = $email;
        $this->username = $username;
    }
}
