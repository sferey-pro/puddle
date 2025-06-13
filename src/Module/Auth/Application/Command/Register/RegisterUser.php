<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\Register;

use App\Module\Auth\Application\DTO\RegisterUserDTO;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Application\Command\CommandInterface;

final class RegisterUser implements CommandInterface
{
    public function __construct(
        public RegisterUserDTO $dto,
        public ?UserId $id = null,
    ) {
    }
}
