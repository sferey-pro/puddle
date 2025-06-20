<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Command;

use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Application\DTO\CreateUserDTO;
use App\Shared\Application\Command\CommandInterface;

final readonly class CreateUser implements CommandInterface
{
    public function __construct(
        public CreateUserDTO $dto,
        public ?UserId $userId = null,
    ) {
    }
}
