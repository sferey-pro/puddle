<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Sequentially;

class CreateUserDTO
{
    #[Sequentially([
        new Assert\NotBlank(message: 'Please enter an email!'),
        new Assert\Email(),
    ])]
    public ?string $email;

    public function __construct(
        ?string $email = null,
    ) {
        $this->email = $email;
    }
}
