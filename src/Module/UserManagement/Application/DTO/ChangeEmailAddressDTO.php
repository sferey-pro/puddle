<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Sequentially;

final readonly class ChangeEmailAddressDTO
{
    #[Sequentially([
        new Assert\NotBlank(message: 'Please enter an email!'),
        new Assert\Email(),
    ])]
    public string $email;
}
