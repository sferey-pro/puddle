<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Core\Application\Command\CommandInterface;
use App\Module\Auth\Application\DTO\RegisterUserDTO;
use App\Module\SharedContext\Domain\ValueObject\UserId;

final class RegisterUser implements CommandInterface
{
    public function __construct(
        public RegisterUserDTO $dto,
        public ?UserId $userId = null,
    ) {
    }
}
