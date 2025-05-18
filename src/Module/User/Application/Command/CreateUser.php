<?php

declare(strict_types=1);

namespace App\Module\User\Application\Command;

use App\Module\Shared\Domain\ValueObject\UserId;
use App\Module\User\Application\DTO\CreateUserDTO;
use App\Shared\Application\Command\CommandInterface;

final readonly class CreateUser implements CommandInterface
{
    public function __construct(
        public CreateUserDTO $dto,
        public ?UserId $identifier = null,
    ) {
    }
}
